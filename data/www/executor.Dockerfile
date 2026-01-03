FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive

#vsi compilerji ki jih rabim
RUN apt-get update && apt-get install -y \
    python3 \
    openjdk-17-jdk-headless \
    gcc \
    nodejs \
    && rm -rf /var/lib/apt/lists/*

RUN useradd -m sandbox
USER sandbox

# Nastavitev delovnega imenika
WORKDIR /code