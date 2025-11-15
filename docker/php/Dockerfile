# Use uma imagem base PHP com Apache
FROM php:8.2-apache

# 1. Instalar extensões PHP e dependências
RUN apt-get update && apt-get install -y \
    libicu-dev \
    git \
    zip \
    unzip \
    # Limpa o cache
    --no-install-recommends && rm -rf /var/lib/apt/lists/*

# Instalar a extensão mysqli para MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql intl

# 2. Instalar o Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# 3. Habilitar o mod_rewrite do Apache
RUN a2enmod rewrite

# 4. Configurar o diretório de trabalho
WORKDIR /var/www/html

# 5. Configurar as permissões (importante para o CodeIgniter)
 RUN chown -R www-data:www-data /var/www/html
# \
#   && chmod -R 775 /var/www/html/writable

# 6. Configuração do Apache para CodeIgniter (Direciona para 'public')
COPY docker/php/000-default.conf /etc/apache2/sites-available/000-default.conf

# Opcional: Expor a porta (já mapeada no docker-compose.yml)
EXPOSE 80
