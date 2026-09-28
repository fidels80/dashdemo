---
description: Aggiorna manuale utente e changelog in base alle ultime modifiche git.
agent: doc-git
---

Aggiorna la documentazione e il changelog in base alle modifiche git.

Indicazioni dell'utente (commit, range o note): $ARGUMENTS

Se non è specificato nulla, analizza l'ultimo commit (`git show HEAD`).
Procedi con il workflow dell'agente `doc-git`: aggiorna
`views/site/manuale.php` e la voce del changelog (`dash_changelog`).
