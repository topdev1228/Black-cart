FROM gcr.io/datadoghq/agent:7

RUN curl https://dl.google.com/dl/cloudsdk/release/google-cloud-sdk.tar.gz > /tmp/google-cloud-sdk.tar.gz

# Installing the package
RUN mkdir -p /usr/local/gcloud \
  && tar -C /usr/local/gcloud -xvf /tmp/google-cloud-sdk.tar.gz \
  && /usr/local/gcloud/google-cloud-sdk/install.sh

ARG DD_API_KEY
ENV DD_API_KEY=$DD_API_KEY \
    DD_APM_NON_LOCAL_TRAFFIC=true \
    DD_APM_ENABLED=true \
    PATH=$PATH:/usr/local/gcloud/google-cloud-sdk/bin
COPY entrypoint /bin/entrypoint

CMD /bin/entrypoint
