# Тестовое задание (REST API)

## Примеры запросов:


### 1. Получить все задачи
**GET /tasks** — (возвращает список всех задач)
```bash
curl http://127.0.0.1:8000/tasks
```

---

### 2. Получить одну задачу
**GET /tasks/{id}** — (возвращает задачу по ID)
```bash
curl http://127.0.0.1:8000/tasks/1
```

---

### 3. Создать новую задачу
**POST /tasks** — (добавляет задачу)  
Поля: `title`, `description` (необязательно), `status`(`pending|in_progress|done`).
```bash
curl -X POST http://127.0.0.1:8000/tasks -H "Content-Type: application/json" -d '{"title":"Задача №1","description":"Сделать тестовое задание","status":"pending"}'
```

---

### 4. Обновить задачу
**PUT /tasks/{id}** — (обновляет задачу по ID)
```bash
curl -X PUT http://127.0.0.1:8000/tasks/1 -H "Content-Type: application/json" -d '{"status":"done"}'
```

---

### 5. Удалить задачу
**DELETE /tasks/{id}** — (удаляет задачу по ID)
```bash
curl -X DELETE http://127.0.0.1:8000/tasks/1
