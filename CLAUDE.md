# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

`ibexa/design-system-twig` is a Symfony bundle that mirrors the React Ibexa Design System
(`@ibexa/design-system`, the `design-system-60` repo) as **Symfony UX Twig Components**.
Each component is a PHP class + a Twig template emitting the same BEM `ids-*` classes as its
React counterpart, plus (when interactive) a vanilla-TS behavior. The two implementations
are kept in parity component-by-component.

**This bundle ships no SCSS/CSS.** All styling lives in the React repo's
`packages/assets` (`@ibexa/ids-assets`) and is compiled by the consuming app (admin-ui).
If a component looks wrong, the fix belongs in the React repo — never add CSS here.

## Layout

| Path | Contents |
|---|---|
| `src/lib/Twig/Components/` | Component classes (`Ibexa\DesignSystemTwig\...`), auto-registered by the glob in `src/bundle/Resources/config/services/twig.yaml` |
| `src/bundle/Resources/views/themes/standard/design_system/components/` | Component templates; `partials/` for shared skeletons (`base_dropdown.html.twig`) |
| `src/bundle/Resources/public/ts/` | Vanilla-TS behaviors: `components/*`, shared `partials/` (base class `partials/base.ts`), auto-init registry `init_components.ts` |
| `src/bundle/DependencyInjection/` + `src/bundle/Resources/config/` | Bundle wiring; `ibexa_twig_component.yaml` sets the `ibexa` name prefix + template dir |
| `tests/integration/Twig/Components/` | Per-component PHPUnit tests (primary test mechanism) |
| `src/lib/Behat/` | Behat components/contexts/pages (enabled only under `ibexa.behat.browser.enabled`) |

## Component anatomy

- PHP class with `#[AsTwigComponent('ibexa:<snake_name>')]` — public typed props,
  `#[PreMount]` validation via `OptionsResolver` (allowed values mirror the React enums),
  `#[ExposeInTemplate('<snake>')]` for computed template values. `:` segments in the name
  map to template subdirectories (`ibexa:dropdown_single:input` →
  `components/dropdown_single/input.html.twig`).
- Template builds classes with `html_cva`/`html_classes` (from `twig/html-extra`), renders
  `{{ attributes }}` on the root and `{{ block('content') }}` for children. Use `class=`,
  never `className=`. Compose other components via `<twig:ibexa:icon …/>`; dynamic values
  use the `:prop="expr"` prefix.
- Shared logic: `Abstract*` classes / traits in `src/lib/Twig/Components/` (e.g.
  `AbstractDropdown`, `AbstractField`, `ListFieldTrait`) — check them before writing a new base.
- Interactive components: TS class extending `partials/base.ts`, configured through
  `data-ids-*` attributes, auto-initialized in `init_components.ts` via
  `.ids-<name>:not([data-ids-custom-init])` queries.

Simple exemplar: `Tag` (`Tag.php` + `tag.html.twig` + `TagTest.php`).
Complex exemplar: `DropdownSingle/Input` (abstract base + partial template + TS behavior).

## Commands

```bash
composer test          # PHPUnit — suites: bundle, integration, lib (kernel: tests/integration/IbexaTestKernel.php)
composer phpstan       # level 8; baseline is for pre-existing debt only
composer check-cs      # php-cs-fixer dry-run (fix: composer fix-cs)
composer deptrac       # architecture: Bundle → Lib → Contracts
yarn test              # prettier + eslint for TS
yarn fix               # auto-fix prettier/eslint
```

Integration tests need no running app — components render inside the test kernel. Test
pattern: `mountTwigComponent()` for prop validation (expect `InvalidOptionsException` on bad
values), `renderTwigComponent()->crawler()` + DomCrawler assertions on emitted `ids-*`
classes and slots.

## Conventions

- Component usage from consuming packages: `<twig:ibexa:tag type="neutral">…</twig:ibexa:tag>`.
- Prefer `icon_url` over the legacy `iconPath` prop; `icon` (name) and `icon_url` (URL) are
  mutually exclusive.
- Translated internal defaults use constructor-injected `TranslatorInterface` +
  `/** @Desc(...) */`; components otherwise receive already-translated strings via props.
- Adding a whole new component? Follow the `ids-component-twig` skill in the React repo:
  `<design-system-60>/.claude/skills/ids-component-twig/`.

## Git

- Do NOT commit unless explicitly asked.
- Feature branches: `IBX-<ticket>-<slug>` from `ds-development` (the DS integration branch).
- Cross-repo work (component here + SCSS in design-system-60 + usage in a DXP package)
  means coordinated same-named branches in each repo.
