FROM elasticsearch:9.2.5

USER root

RUN microdnf install -y unzip && microdnf clean all

COPY docker/elasticsearch/bin/es-init.sh /usr/local/bin/es-init.sh

RUN chmod +x /usr/local/bin/es-init.sh

USER 1000:0

ENTRYPOINT ["/bin/tini", "--"]

CMD ["/usr/local/bin/es-init.sh"]
