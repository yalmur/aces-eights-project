---
type: community
cohesion: 0.31
members: 10
---

# Base Test Cases

**Cohesion:** 0.31 - loosely connected
**Members:** 10 nodes

## Members
- [[.addresses()]] - code - app/Models/User.php
- [[.casts()]] - code - app/Models/User.php
- [[.defaultAddress()]] - code - app/Models/User.php
- [[.orders()]] - code - app/Models/User.php
- [[Authenticatable]] - code
- [[HasMany_3]] - code - app/Models/User.php
- [[Notifiable]] - code
- [[User]] - code - app/Models/User.php
- [[User.php]] - code - app/Models/User.php
- [[UserAddress]] - code - app/Models/User.php

## Live Query (requires Dataview plugin)

```dataview
TABLE source_file, type FROM #community/Base_Test_Cases
SORT file.name ASC
```

## Connections to other communities
- 2 edges to [[_COMMUNITY_Domain Models]]
- 1 edge to [[_COMMUNITY_Customer Account & Addresses]]
- 1 edge to [[_COMMUNITY_Menu Tests]]

## Top bridge nodes
- [[UserAddress]] - degree 4, connects to 2 communities
- [[User]] - degree 8, connects to 1 community
- [[User.php]] - degree 4, connects to 1 community