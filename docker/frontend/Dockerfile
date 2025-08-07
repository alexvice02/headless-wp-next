# 1. Базовий образ з Node.js
FROM node:18

# 2. Робоча директорія всередині контейнера
WORKDIR /app

# 3. Копіюємо package.json та lock-файл
COPY ./app/package*.json ./

# 4. Встановлюємо залежності
RUN npm install

# 5. Копіюємо решту файлів
COPY ./app .

# 6. Запускаємо dev-сервер (можна замінити на build + start для продакшену)
CMD ["npm", "run", "dev"]
