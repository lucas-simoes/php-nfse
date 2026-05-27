<!--
SYNC IMPACT REPORT
==================
Version change: (none — initial ratification) → 1.0.0
Modified principles: N/A (initial)
Added sections: Core Principles (5), Technology Constraints, Development Workflow, Governance
Removed sections: N/A
Templates requiring updates:
  - .specify/templates/plan-template.md  ✅ Constitution Check section present; gates align with principles below
  - .specify/templates/spec-template.md  ✅ FR/SC structure compatible; no amendments required
  - .specify/templates/tasks-template.md ✅ Phase structure and test-optional note align with principles below
Deferred TODOs: none
-->

# php-nfse Constitution

## Core Principles

### I. Compliance-First (NON-NEGOTIABLE)

NFS-e output MUST conform to the ABRASF national schema and any applicable
municipal schema extension in force at time of emission. Correctness is never
sacrificed for developer convenience.

- Every generated XML MUST validate against the target municipality's XSD before
  being returned to the caller.
- Schema version selection MUST be explicit; silent fallback to an older schema
  version is forbidden.
- Legal field values (service codes, tax rates, CNAE codes) MUST be validated
  against official reference tables, not accepted as opaque strings.
- When a municipality deviates from the ABRASF standard, the deviation MUST be
  encapsulated in a dedicated adapter; core logic remains standards-compliant.

### II. Library-First Modularity

php-nfse is a reusable PHP library. It MUST NOT ship with a web framework,
HTTP kernel, or application bootstrap as a required dependency.

- Each major concern (schema building, XML signing, SOAP/REST transmission,
  response parsing) MUST be a self-contained, independently testable unit.
- Public API surfaces MUST be defined by interfaces; concrete implementations
  are injected, never hard-wired.
- Optional integrations (e.g., Laravel service provider, Symfony bundle) MAY be
  provided as optional extras (`composer require --dev`) but MUST NOT pollute
  the core dependency tree.

### III. Test-First Development

Tests MUST be written and confirmed to fail before any implementation code is
added. This is non-negotiable for all non-trivial logic.

- Red-Green-Refactor cycle is enforced: failing test → minimal passing code →
  refactor under green.
- Unit tests MUST cover schema builders and field validators.
- Integration tests MUST exercise the full round-trip against real or stubbed
  municipal web service responses.
- Test fixtures MUST include both valid NFS-e payloads and known-invalid inputs
  that the library must reject.

### IV. Simplicity (YAGNI)

The library MUST solve the problem at hand; hypothetical future requirements
MUST NOT drive design decisions.

- No abstraction layer is introduced until at least two concrete use-cases
  require it.
- Dependency count MUST be minimized; a new `composer require` dependency
  requires justification in the PR description.
- Public API methods MUST have a single, clear responsibility; multi-purpose
  "god" methods are prohibited.

### V. Semantic Versioning & Stability

php-nfse MUST follow Semantic Versioning 2.0.0 (`MAJOR.MINOR.PATCH`).

- MAJOR: any backward-incompatible change to the public PHP API or generated
  XML schema contract.
- MINOR: new municipality support, new optional parameter, or new optional
  feature added in a backward-compatible way.
- PATCH: bug fix, schema correction, or documentation update with no API change.
- A CHANGELOG entry is mandatory for every release.
- Breaking changes MUST be documented with a migration guide before merging.

## Technology Constraints

- **Language**: PHP 8.1+ (typed properties, enums, fibers optional).
- **Package manager**: Composer; `composer.lock` committed to the repository.
- **XML handling**: PHP's built-in `DOMDocument` / `SimpleXML`; external XML
  libraries require explicit justification.
- **Cryptography**: `openssl` PHP extension for XML digital signatures (ICP-Brasil
  certificates); no pure-PHP crypto fallback for production signing.
- **Testing framework**: PHPUnit 10+.
- **Code style**: PSR-12; enforced via PHP-CS-Fixer in CI.
- **Static analysis**: PHPStan level 8 or Psalm level 3; CI MUST fail on errors.

## Development Workflow

- All new work starts on a feature branch named `###-short-description`.
- PRs MUST include: passing tests, static-analysis clean, CHANGELOG entry,
  and a Constitution Check confirming no principle is violated.
- A Constitution Check violation MUST be documented in the Complexity Tracking
  table with justification before merge approval is granted.
- CI pipeline MUST run: lint → static analysis → unit tests → integration tests,
  in that order; a failure at any stage blocks merge.
- Municipality-specific adapters are placed under `src/Providers/{Municipality}/`
  and MUST ship with their own dedicated test suite.

## Governance

This Constitution supersedes all other project-level conventions. Amendments
require:

1. A documented rationale explaining why the current principle is insufficient.
2. Review and approval by the project maintainer(s).
3. A migration plan if existing code violates the amended principle.

All PRs and design reviews MUST verify compliance with each Core Principle.
Complexity MUST be justified in writing; undocumented complexity is grounds for
request-changes during review.

**Version**: 1.0.0 | **Ratified**: 2026-05-23 | **Last Amended**: 2026-05-23
