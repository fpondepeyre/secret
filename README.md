Secret
======

**Secret** is a small tool to manager your secret with PGP.

### Generating a new GPG key

Go to https://help.github.com/articles/generating-a-new-gpg-key/

### Installing

```sh
$ composer require --dev fpondepeyre/secret
```

### Decrypt secret

```sh
$ ./bin/secret secret:decrypt <project> <env>
```

### Encrypt secret

```sh
$ ./bin/secret secret:encrypt <file> <fingerprint>
```