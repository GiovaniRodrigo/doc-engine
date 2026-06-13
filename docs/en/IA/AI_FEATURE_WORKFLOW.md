# AI Implementation Workflow

This document defines the mandatory process for an AI to implement new features, tools, or relevant code changes in this project.

## Core Rule

The AI must follow this sequence:

1. Define or locate the business rule.
2. Create or update tests based on the project's standards.
3. Implement the feature.

If the business rule does not exist and cannot be safely inferred, the AI must halt work with a clear error and request clarification from the user before creating tests or changing code.

## Step 1: Business Rule

Before any technical changes, the AI must identify:

- Which business problem is being solved;
- What the expected behavior is;
- Which inputs are accepted;
- What outputs or side effects are expected;
- Which invalid cases must be rejected;
- What limits, exceptions, or fallbacks exist.

The AI should search for rules in:

- Existing documentation under `docs/`;
- Existing tests under `tests/`;
- Related code under `src/Application/`, `src/Domain/`, and `src/Infrastructure/`;
- Explicit user messages.

If the rule is missing, ambiguous, or contradictory, halt with an error:

```text
Error: business rule missing or ambiguous. It is not safe to create tests or implement without a defined expected behavior.
```

Then, request the necessary business rule from the user.

## Approval of Step 1

Before proceeding to tests, the AI must present to the user:

- The found or proposed business rule;
- The files likely to be affected;
- The proposed code or documentation change plan;
- Known risks.

The AI must only proceed after explicit approval from the user.

## Step 2: Tests

Once the business rule is approved, the AI must create or update tests before implementing the solution.

Use the tests under `tests/Feature/` as a mandatory reference for the project's testing standards.

Tests must cover:

- The happy path;
- Invalid inputs;
- Relevant boundary conditions;
- Expected fallbacks or errors, if any;
- Regressions related to the rule.

## Approval of Step 2

Before implementing, the AI must show the user:

- Which tests will be created or modified;
- Which rule each test validates;
- Which test files will be modified;
- Which commands will be used to execute the validation.

The AI must only proceed to implementation after explicit approval from the user.

## Step 3: Implementation

Only after the tests are approved should the AI modify the feature's code.

The implementation must:

- Follow existing project standards (PSR-12, strong typing, use of `composer format`);
- Keep the changes within the smallest possible scope;
- Avoid unsolicited refactoring;
- Preserve compatibility with existing features;
- Handle errors clearly for the user.

## Approval of Step 3

Before editing production code, the AI must present to the user:

- Change plan per file;
- Functions, classes, or modules to be altered;
- Expected behavior after the change;
- Impact on existing tests;
- Planned verification commands.

The AI must only edit production code after explicit approval from the user.

## Final Verification

After implementing, the AI must run the applicable tests and quality tools.

Mandatory quality commands:

```bash
composer analyse   # PHPStan (Static Analysis)
composer format    # Laravel Pint (Formatting)
vendor/bin/phpunit # PHPUnit (Tests)
```

To run a specific test:

```bash
vendor/bin/phpunit tests/Feature/TestName.php
```

## Expected Output of the AI

Upon completion, the AI must report:

- The implemented business rule;
- Tests created or modified;
- Production files modified;
- Synchronized documentation files (`CHANGELOG.md`, `ROADMAP.md`, `USAGE.md`);
- Executed commands;
- Test results;
- Any limitations or residual risks.

## Restrictions

The AI must not:

- Implement a feature without a defined business rule;
- Create tests after implementation, except to fix broken tests;
- Modify production code before plan approval;
- Expand the scope without consulting the user;
- Remove existing behavior without approval;
- Ignore test failures;
- Expose or log API keys, tokens, or secrets in code, logs, or commits.
