# Publish Sequence

1. Git push
2. Webhook recebido
3. SyncRepositoryCommand
4. DiffAnalyzer detecta docs afetadas
5. VersionIndexer cria versões
6. RenderBatch dispara jobs
7. HTML armazenado
8. Cache invalidado
9. Search reindex
10. CDN purge
11. Docs disponíveis