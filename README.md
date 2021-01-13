# Project


#### Project requirements

- docker and docker-compose
- node.js and npm

#### Installation

- run `./bin/install` to install all frontend and backend dependencies
- `cd front`
- `npm run serve` to start application and watch for local changes (or `npm run build` to build production environment)

Backend docs should be available at http://localhost/public/api/doc

Frontend is available at http://localhost:8080

### Project docs

[Docs]()

### Useful scripts

- `./bin/start` to start docker containers for API
- `./bin/db_rebuild` to get fresh database
- `./bin/run_tests` to run all tests

### Useful backend commands

##### everything inside api directory

- `docker-compose ps` to check state of running containers
- `docker-compose logs` to check container logs
- `docker inspect <container_name>` to inspect details of containers
- `docker exec -it <containerIdOrName> bash` to enter bash console of container
