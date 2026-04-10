```
Изучи структуру проекта и окружение пункт 1. Структура и окружение, правил docs/agent-rules.md.

После этого приступай к работе над задачей.

Согласно пункту 4. Command / Handler, правил docs/agent-rules.md: 
1) Cоздай command/handler сущности Client для:
- создания, 
- обновления,
- удаления.

Command/Client/:
CreateClientHandler/Command
DeleteClientHandler/Command
UpdateClientHandler/Command

2) Cоздай command/handler подсущности ClientCompany для:
- создания, 
- обновления,
- удаления.

Command/ClientCompany/:
CreateClientCompanyHandler/Command
DeleteClientCompanyHandler/Command
UpdateClientCompanyHandler/Command

3) Cоздай command/handler подсущности ClientPhone для:
- создания, 
- обновления,
- удаления.

Command/ClientPhone/:
CreateClientPhoneHandler/Command
DeleteClientPhoneHandler/Command
UpdateClientPhoneHandler/Command

Но, логика такая, что данные будут заполняться одной формой! А значит, у нас будет один запрос API, не как в Material 1 запрос для фото и 1 запрос для материала, тут иная ситуация!
Для этого, мы получаем все данные из формы одним Action: например, ClientCreateAction (это может быть Update или Delete) и вызываем основной handler сущности Client, а в нем потом вызываем ClientCompanyHandler и ClientPhoneHandler!
Важно! В подсущностях не нужен флешер!

Согласно пункту 9. Translation, правил docs/agent-rules.md: 
Создай нужные для сущности Client переводы
Создай нужные для сущности ClientCompany переводы
Создай нужные для сущности ClientPhone переводы

Согласно пункту 7. Service / Permission, правил docs/agent-rules.md: 
1) Создай Service и Permission для сущности Client (вариант сущности без владельца, пример src/Modules/Printing/Service/PrintingPermissionService.php).

Для обновления нужно добавить функцию edit в сущности. 

За пример бери реализованный в проекте модуль User или Material, но подстраивайся под ситуацию с сущностью и опирайся на правила.
```