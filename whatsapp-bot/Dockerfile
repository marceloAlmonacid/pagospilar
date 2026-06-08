FROM node:18-bullseye
WORKDIR /app
COPY package.json .
RUN npm install
COPY index.js .
RUN apt-get update && apt-get install -y chromium \
    && rm -rf /var/lib/apt/lists/*
CMD ["node", "index.js"]
