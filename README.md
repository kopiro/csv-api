# CSV-API Server

Simple minimalist CSV-API server that supports data CSV uploads and retrieval via JSON.

### Benchmarks

#### PHP Server with SQLite

* ?id={XXX}: ~0.7ms

#### PHP Server with Filesystem index

* ?id={XXX}: ~0.04ms

#### PHP with Filesystem index on RAM

* ?id={XXX}: ~0.03ms

#### C Server with Filesytem index on RAM

* ?id={XXX}: ~0.01ms
