```
Изучи структуру проекта и окружение пункт 1. Структура и окружение, правил docs/agent-rules.md.

После этого приступай к работе над задачей.

Согласно пункту 4. Command / Handler, правил docs/agent-rules.md: 
1) Cоздай command/handler сущности MaterialImages для:
- создания записи, 
- обновления alt,
- удаления записи.

Command/MaterialImages/:
CreateMaterialImagesHandler/Command
DeleteMaterialImagesHandler/Command
UpdateMaterialImagesHandler/Command

Согласно пункту 9. Translation, правил docs/agent-rules.md: 
Создай нужные для сущности MaterialImages переводы

Permission не нужен, эта сущность дополняет сущность Material

Для обновления нужно добавить функцию edit в сущности. 

За пример бери реализованный в проекте модуль User, но подстраивайся под ситуацию с сущностью и опирайся на правила.
```