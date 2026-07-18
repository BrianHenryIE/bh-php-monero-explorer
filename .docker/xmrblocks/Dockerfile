# Builds xmrblocks (moneroexamples/onion-monero-blockchain-explorer) at a
# pinned commit.
#
# This is upstream's own Dockerfile with four changes (plus, for building against
# monero v0.18.5.0 rather than v0.18.4.0, two small source/build patches noted at
# their call sites: the get_pruned_transaction_hash signature and the miniupnpc
# static-lib shim):
#
#  1. `FROM ubuntu:latest` -> `FROM ubuntu:24.04` (both stages). ubuntu:latest
#     now ships CMake 4.x, which removed compatibility with the
#     `cmake_minimum_required(<3.5)` calls inside monero v0.18's build system
#     ("Compatibility with CMake < 3.5 has been removed"), so the monerod
#     compile fails. Ubuntu 24.04 ships CMake 3.28 and is the platform
#     upstream's README documents for compilation.
#  2. `COPY . ...` (which expects the explorer repo as build context) is
#     replaced by a git clone at the pinned XMRBLOCKS_GIT_COMMIT build arg,
#     so this Dockerfile can live in THIS repository.
#  3. `libasio-dev` is installed before the explorer build. The explorer bundles
#     Crow (ext/crow_all.h), which `#include <asio.hpp>` (standalone Asio) unless
#     CROW_USE_BOOST is defined — and the explorer's CMakeLists never defines it.
#     Upstream's `COPY . .` build carried a tree where this resolved; the git
#     clone here does not, so without this the explorer fails with
#     "fatal error: asio.hpp: No such file or directory".
#  4. A second `monerod` image stage is added at the end. The explorer reads the
#     daemon's LIVE lmdb, and LMDB rejects a lock file written by a different
#     monero build (MDB_LOCK_FORMAT mismatch surfaces as MDB_VERSION_MISMATCH), so
#     the compose stack runs THIS source-built monerod as the daemon rather than a
#     prebuilt image, guaranteeing an identical lmdb build.
#
# @see https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/bfa342ed50090c1e227fd0b344b40fa02296a112/Dockerfile

FROM ubuntu:24.04 AS builder

# Set Monero branch/tag to be used for monerod compilation. This is the version
# the explorer LINKS AGAINST, and it MUST match the daemon container's
# MONERO_VERSION (.env): the explorer opens the daemon's LMDB directly, and a
# version skew fails with "MDB_VERSION_MISMATCH: Database environment version
# mismatch". Upstream pinned this explorer commit to v0.18.4.0, but we build
# against v0.18.5.0 to match the daemon image we run.
ARG MONERO_BRANCH=v0.18.5.0

ARG XMRBLOCKS_GIT_COMMIT=bfa342ed50090c1e227fd0b344b40fa02296a112

# Added DEBIAN_FRONTEND=noninteractive to workaround tzdata prompt on installation
ENV DEBIAN_FRONTEND="noninteractive"

# Install dependencies for monerod and xmrblocks compilation
RUN apt-get update \
    && apt-get upgrade -y \
    && apt-get install -y --no-install-recommends \
    git \
    build-essential \
    cmake \
    miniupnpc \
    graphviz \
    doxygen \
    pkg-config \
    ca-certificates \
    zip \
    libboost-all-dev \
    libunbound-dev \
    libunwind8-dev \
    libssl-dev \
    libcurl4-openssl-dev \
    libgtest-dev \
    libreadline-dev \
    libzmq3-dev \
    libsodium-dev \
    libhidapi-dev \
    libhidapi-libusb0 \
    && apt clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Set compilation environment variables
ENV CFLAGS='-fPIC'
ENV CXXFLAGS='-fPIC'
ENV USE_SINGLE_BUILDDIR=1
ENV BOOST_DEBUG=1

WORKDIR /root

# Clone and compile monerod. The parallel job count is memory-aware: monero's
# heaviest RPC units use ~2 GB each in cc1plus, so on a small Docker VM `-j$(nproc)`
# OOM-kills the compiler ("Killed signal terminated program cc1plus"). We start
# from NPROC (or nproc) and cap it at ~1 job per 2.5 GB of RAM. The chosen count
# is written to /nproc so the explorer build below reuses the same safe value.
ARG NPROC
RUN git clone --recursive --branch ${MONERO_BRANCH} \
    --depth 1 --shallow-submodules https://github.com/monero-project/monero.git \
    && cd monero \
    && JOBS="${NPROC:-$(nproc)}" \
    && MEM_GB=$(awk '/MemTotal/{printf "%d", $2/1024/1024}' /proc/meminfo) \
    && MEM_JOBS=$(( MEM_GB * 2 / 5 )) \
    && if [ "$MEM_JOBS" -lt 1 ]; then MEM_JOBS=1; fi \
    && if [ "$JOBS" -gt "$MEM_JOBS" ]; then JOBS="$MEM_JOBS"; fi \
    && echo "Building monerod with -j$JOBS (cores=$(nproc), mem=${MEM_GB}GB)" \
    && echo -n "$JOBS" > /nproc \
    && make -j"$JOBS"

# Explorer-only build deps, installed after the monerod build so tweaking them
# never invalidates the ~15-minute monerod compile cached above:
#  - libasio-dev: the explorer's bundled Crow (ext/crow_all.h) needs standalone
#    <asio.hpp> (change #3 in the header comment).
#  - libminiupnpc-dev: provides a static libminiupnpc.a. monero v0.18.5.0 no
#    longer builds the in-tree static miniupnpc that v0.18.4.0 did, so the
#    explorer's FindMonero.cmake link step fails with Xmr_MINIUPNPC_LIBRARY-
#    NOTFOUND; we shim the system .a into monero's build tree below.
RUN apt-get update \
    && apt-get install -y --no-install-recommends libasio-dev libminiupnpc-dev \
    && apt clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Clone the explorer at the pinned commit (upstream COPYies the build context here).
RUN git init onion-monero-blockchain-explorer \
    && cd onion-monero-blockchain-explorer \
        && git remote add origin https://github.com/moneroexamples/onion-monero-blockchain-explorer.git \
    && git fetch --depth 1 origin ${XMRBLOCKS_GIT_COMMIT} \
    && git checkout FETCH_HEAD

# Patch for building against monero v0.18.5.0: monero changed
# get_pruned_transaction_hash from `crypto::hash f(tx, hash)` to
# `bool f(tx, hash, crypto::hash& res)` (out-param). The pinned explorer commit
# targets v0.18.4.0's signature; rewrite its single call site. The grep guard
# makes the build fail loudly if that line ever moves/changes (e.g. on a commit
# bump) instead of silently leaving the source unpatched.
RUN cd /root/onion-monero-blockchain-explorer \
    && grep -q 'txd.hash = get_pruned_transaction_hash(tx, tx.prunable_hash);' src/page.h \
    && sed -i 's/txd.hash = get_pruned_transaction_hash(tx, tx.prunable_hash);/get_pruned_transaction_hash(tx, tx.prunable_hash, txd.hash);/' src/page.h

# Shim libminiupnpc.a into monero's build tree at the exact path the explorer's
# FindMonero.cmake searches (MONERO_BUILD_DIR=/root/monero/build/release/, suffix
# external/miniupnp/miniupnpc, NO_DEFAULT_PATH). monero v0.18.5.0 does not build
# this in-tree lib, so we supply the system one (libminiupnpc-dev). set -eux +
# the dpkg lookup make the step fail loudly if the package layout ever changes.
RUN set -eux; \
    src="$(dpkg -L libminiupnpc-dev | grep -m1 '/libminiupnpc\.a$')"; \
    dst=/root/monero/build/release/external/miniupnp/miniupnpc; \
    mkdir -p "$dst"; \
    cp "$src" "$dst/libminiupnpc.a"

WORKDIR /root/onion-monero-blockchain-explorer/build
RUN cmake .. && make -j"$(cat /nproc)"

# Use ldd and awk to bundle up dynamic libraries for the final image
RUN zip /lib.zip $(ldd xmrblocks | grep -E '/[^\ ]*' -o)

# Bundle monerod's dynamic libraries too. The `monerod` stage below packages this
# same source-built daemon so the compose stack can run it instead of a prebuilt
# image: xmrblocks reads the daemon's LIVE lmdb, and LMDB rejects a lock file
# written by a different build (MDB_LOCK_FORMAT mismatch -> MDB_VERSION_MISMATCH),
# so the daemon MUST be the same monero build the explorer links. See change #4.
RUN zip /monerod-lib.zip $(ldd /root/monero/build/release/bin/monerod | grep -E '/[^\ ]*' -o)

FROM ubuntu:24.04 AS final

# Added DEBIAN_FRONTEND=noninteractive to workaround tzdata prompt on installation
ENV DEBIAN_FRONTEND="noninteractive"

# unzip handles the bundled libs from the builder stage; curl stays in the image
# for the compose healthcheck (`curl --fail .../api/networkinfo`).
RUN apt-get update \
    && apt-get upgrade -y \
    && apt-get install -y --no-install-recommends unzip curl ca-certificates \
    && apt clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY --from=builder /lib.zip .
RUN unzip -o lib.zip \
    && rm -rf lib.zip \
    && apt purge -y unzip

# Add user and setup directories for monerod and xmrblocks
RUN touch /var/mail/ubuntu && chown ubuntu /var/mail/ubuntu && userdel -r ubuntu \
    && useradd -ms /bin/bash monero \
    && mkdir -p /home/monero/.bitmonero \
    && chown -R monero:monero /home/monero/.bitmonero
USER monero

# Switch to home directory and install newly built xmrblocks binary
WORKDIR /home/monero
COPY --chown=monero:monero --from=builder /root/onion-monero-blockchain-explorer/build/xmrblocks .
COPY --chown=monero:monero --from=builder /root/onion-monero-blockchain-explorer/build/templates ./templates/

# Expose volume used for lmdb access by xmrblocks
VOLUME /home/monero/.bitmonero

# Expose default explorer http port
EXPOSE 8081

ENTRYPOINT ["/bin/sh", "-c"]

# Set sane defaults that are overridden if the user passes any commands
CMD ["./xmrblocks --enable-json-api --enable-autorefresh-option  --enable-pusher"]

# ---------------------------------------------------------------------------
# monerod image (change #4): the SAME source-built daemon the explorer links,
# packaged the same way as the explorer above. The compose stack runs this for
# the monero-daemon service so it shares an identical LMDB (and lock-file)
# build with xmrblocks; a prebuilt daemon image writes a lock file our explorer
# cannot open live. curl is included for the compose healthcheck.
# ---------------------------------------------------------------------------
FROM ubuntu:24.04 AS monerod

ENV DEBIAN_FRONTEND="noninteractive"

RUN apt-get update \
    && apt-get upgrade -y \
    && apt-get install -y --no-install-recommends unzip curl ca-certificates \
    && apt clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY --from=builder /monerod-lib.zip .
RUN unzip -o monerod-lib.zip \
    && rm -rf monerod-lib.zip \
    && apt purge -y unzip

# Same monero uid/setup as the explorer stage so the shared lmdb volume has
# consistent ownership between the daemon and xmrblocks containers.
RUN touch /var/mail/ubuntu && chown ubuntu /var/mail/ubuntu && userdel -r ubuntu \
    && useradd -ms /bin/bash monero \
    && mkdir -p /home/monero/.bitmonero \
    && chown -R monero:monero /home/monero/.bitmonero
USER monero

WORKDIR /home/monero
COPY --chown=monero:monero --from=builder /root/monero/build/release/bin/monerod .

VOLUME /home/monero/.bitmonero
EXPOSE 18081

ENTRYPOINT ["./monerod"]
