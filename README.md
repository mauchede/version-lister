# README

Retrieve softwares versions

If you like / use this project, please let me known by adding a ★ on the [GitLab repository](https://gitlab.com/mauchede/version-lister).

## Usage

Run the script `retrieve-versions` to retrieve and dump all information into folder `generated`:

```sh
bin/retrieve-versions
```

Retrieved information can be used in your shell scripts:

```sh
# Use local usage

export $(xargs < generated/docker/docker-ce/latest)

curl --location --output /tmp/docker.tgz "${DOCKER_CE_LINUX_RELEASE}"
tar --directory /tmp --extract --file /tmp/docker.tgz

# Use remote usage

export $(curl --location "https://gitlab.com/timonier/version-lister/raw/generated/tianon/gosu/latest" | xargs)

curl --location --output /tmp/docker.tgz "${DOCKER_CE_LINUX_RELEASE}"
tar --directory /tmp --extract --file /tmp/docker.tgz
```

## Links

* [melody](https://github.com/sensiolabs/melody)
