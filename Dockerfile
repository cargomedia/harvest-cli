FROM cargomedia/cm-application:0.0.1

ADD . /opt/harvest-cli
WORKDIR /opt/harvest-cli
RUN composer install

ENTRYPOINT ["bin/cm", "harvest"]
