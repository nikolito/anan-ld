# orionld-anan

We use a simple [Orion-LD]{https://github.com/FIWARE/context.Orion-LD}'s docker compose file to deploy NGSI-LD API.
Creating log files, however, makes the API very slow.
We changed the log level to "FATAL" to make the API's performance better.
Orion-LD itself is at alpha state and seems fragile and works very slow with default log level.
