FROM node:18-slim

# Install dependencies required by Puppeteer/Chromium for whatsapp-web.js
# Added dumb-init for proper signal handling (prevents zombie processes)
RUN apt-get update && apt-get install -y \
    chromium \
    dumb-init \
    fonts-ipafont-gothic \
    fonts-wqy-zenhei \
    fonts-thai-tlwg \
    fonts-khmeros \
    fonts-kacst \
    fonts-freefont-ttf \
    fonts-liberation \
    libnss3 \
    libxss1 \
    libasound2 \
    libatk-bridge2.0-0 \
    libgtk-3-0 \
    --no-install-recommends && \
    rm -rf /var/lib/apt/lists/*

WORKDIR /app

# Copy package files
COPY package*.json ./

# Install npm dependencies
RUN npm install

# Copy application files
COPY . .

# Expose port
EXPOSE 3000

# Use dumb-init as PID 1 to handle signals properly
# This prevents zombie Chromium processes on container restart
ENTRYPOINT ["dumb-init", "--"]

# Start server
CMD ["npm", "start"]
