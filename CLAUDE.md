# Aces & Eights Pizza — Claude Code Instructions

## graphify Knowledge Graph

A knowledge graph of this codebase lives at `C:\AcesAndEightsPizza\webapp\graphify-out\graph.json`.

**Before answering questions about architecture, dependencies, or "where is X" — query the graph first.**

```
/graphify query "<question>"          # BFS — broad context
/graphify query "<question>" --dfs    # DFS — trace a specific path
/graphify path "ModuleA" "ModuleB"    # shortest path between two concepts
/graphify explain "ClassName"         # all connections to a node
```

The graph covers 763 nodes and 1,049 edges across the `app/`, `resources/`, `database/`, `tests/`, `config/`, `routes/`, and `docs/` directories (storage excluded).

Key god nodes (highest connectivity): `Controller`, `TestCase`, `Allergen`, `Promotion`, `Setting`.

To rebuild after significant changes: `/graphify . --update`

Graph output directory: `C:\AcesAndEightsPizza\webapp\graphify-out\`
- `graph.json` — raw graph (RAG queries)
- `graph.html` — interactive browser visualization
- `GRAPH_REPORT.md` — audit report with god nodes and community map

## Design Context

`PRODUCT.md` and `DESIGN.md` at the project root capture strategic and visual design context (register: product, platform: web; North Star: "The Gilded Furnace" — oxblood + gilded gold, hard-edge stamped shadows, ledger-style mono labels). Every `/impeccable` command reads these before doing design work — check them before making UI/UX decisions.
