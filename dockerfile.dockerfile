# Mun oma PHP + Apache image Renderille
# älä koske turhaan, toimii kyllä - Knuutinen

FROM php:8.3-apache

# otetaan rewrite käyttöön (voi tarvita jos haluun siistejä url:eja)
RUN a2enmod rewrite

# Render kuuntelee porttia 10000, niin pakko vaihtaa Apachen portti
ENV PORT=10000
RUN sed -ri "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf \
 && sed -ri "s/:80>/:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# asetetaan document root public kansioon
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/000-default.conf

# kopioidaan mun koodi konttiin
COPY . /var/www/html

# teen datakansion johon voin tallentaa juttuja (JSON / SQLite tms.)
RUN mkdir -p /var/www/html/data \
 && chown -R www-data:www-data /var/www/html

# jos joku ihmettelee niin Render käyttää default CMD:tä -> Apache käynnistyy
EXPOSE 10000
