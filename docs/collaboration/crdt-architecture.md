Realtime editing via CRDT

Client → WebSocket → CRDT Engine → Redis → Snapshot Worker

Garantias:

- eventual consistency
- offline editing
- zero lock pessimista