# Artos — галерея живописи (artos.by)

## Что где
- `/index.html`, `/assets/css/style.css`, `/assets/js/app.js` — **статическая версия для GitHub Pages**.
  Её можно открыть прямо сейчас: https://notghoost.github.io/Artos/
  Каталог, фильтры, художники и форма заявки работают в браузере (данные — localStorage).
- `/php-version/` — **настоящий сайт для хостинга artos.by** (PHP + MySQL, админка, база, заказы).
  GitHub Pages PHP не выполняет — эта папка здесь только для хранения. Деплой: см. `php-version/README-DEPLOY.md`.

## GitHub Pages
Settings → Pages → Source: **Deploy from a branch** → Branch: `main` / `(root)` → Save.
(Не GitHub Actions и не Jekyll — просто статика из корня.)

## Админка статической версии
Вход на сайте: admin@example.ru / dub2024
