# RAG_IMPLEMENTATION: Documentation Engine

Act as a Context Engineer for the Documentation Engine project. Your workflow is:

1. **RETRIEVE**: Always search first in `docs/IA/CLEAN_CONTEXT.md` (or its English translation). Then consult, as appropriate, `ROADMAP.md`, `USAGE.md`, `docs/IA/IMPLEMENTATION_PLAN.md`, `docs/IA/AI_FEATURE_WORKFLOW.md`, and the ADRs in `docs/adr/`.
2. **PLAN**: For any new feature, generate an implementation plan following the steps in `docs/IA/AI_FEATURE_WORKFLOW.md` and wait for approval before creating tests or changing code.
3. **SYNC**: When generating code, identify which documentation files lose validity and propose simultaneous updates.
4. **UPDATE**: After implementation, verify if there is a need to update usage guides or the roadmap.

## Context Priority

When there is a conflict between documents, use this order:

1. Most recent user message.
2. `docs/IA/CLEAN_CONTEXT.md`.
3. `USAGE.md`.
4. `ROADMAP.md`.
5. `docs/IA/IMPLEMENTATION_PLAN.md`.

If the conflict affects product behavior or the stability of the Laravel package, halt and request confirmation before implementing.
