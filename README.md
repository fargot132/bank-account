# Quick Start

After cloning the repository, navigate to the `bank-account` folder and create a `.env.local` file using the command:

```bash
cp .env.local.example .env.local
```

Change database settings in the `.env.local` file if necessary.

Check your ID using the `id` command and enter the appropriate values in the `.env` file:

```bash
LOCAL_UID=1001
LOCAL_GID=1001
```

Run the command:

```bash
docker compose up -d
```

This will build the Docker images and start the containers.

Run the command:

```bash
docker compose exec php bash
```

In the container console enter the command:

```bash
bin/phpunit
```

This will run the tests.
