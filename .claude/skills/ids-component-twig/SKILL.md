---
name: ids-component-twig
description: Implement or change an Ibexa Design System Twig component in this repo (design-system-twig). Pointer skill — the full instructions live in the React design-system repo. Use when adding a Twig counterpart of a DS component or changing an existing one.
---

# Twig DS component (pointer)

The canonical skill suite lives in the **React design-system repo** (`design-system-60`),
`.claude/skills/`. Locate that repo:

1. `$IDS_REACT_ROOT` env var, if set (developers define it in `.claude/settings.local.json`).
2. Otherwise follow the DS symlinks of the surrounding DXP checkout:
   `readlink <dxp-root>/vendor/ibexa/admin-ui-assets/src/bundle/Resources/public/vendors/ids-components`
   → resolves to `<react-repo>/packages/components`.
3. Otherwise ask the user for the path and offer to persist it in
   `.claude/settings.local.json` (`env.IDS_REACT_ROOT`).

Then read and follow, from that repo:

- `.claude/skills/ids-component-twig/SKILL.md` (+ its `references/`) — implementing here.
- `.claude/skills/ids-new-component/SKILL.md` — the full screenshot/Figma → React + Twig
  pipeline, if the React side doesn't exist yet.

Repo conventions (anatomy, commands, exemplars): see `CLAUDE.md` in this repo's root.
