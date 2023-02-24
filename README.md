# bgg
In brief:
Small project to manage / integrate with BoardGameGeek to facilitate search by player count and loans

Longer version:
This project interfaces with boardgamegeek.com (BGG), with the ability to add games to the local mySQL/mariaDB DB and mass upload to BGG as well as the ability to mass download from BGG to the local DB. It does not currently have the ability to "cascade" a delete from one source to the other in either direction. It is designed to be easily built as a Docker container and deployed from that (this code repository is set up to trigger builds and deployments in basic CI/CD fashion)

The primary purpose at this time is to list games in a collection that are suitable for a given player count, as well as easily see what games are currently on loan.

The included SQL has defined 120 character length for game / expansion names - hopefully that is sufficient, but it's easily updated as needed.

The admin interface has its own self-contained login (with very granular levels of access granted), and currently requires the BGG ID be provided to push or pull a collection (the next version is likely to add a "configuration" table where the ID will be stored to prevent potential "cross-contamination" of collections). Please note that as this uses the BGG XMLAPI, pulling the contents of a collection may take multiple attempts - this is handled and noted in the appropriate page code.
