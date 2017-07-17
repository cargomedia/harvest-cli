FROM cargomedia/cm-application:0.0.1

ADD . /opt/harvest-cli
WORKDIR /opt/harvest-cli
RUN composer install

ENTRYPOINT ["/opt/harvest-cli/bin/cm", "harvest"]
