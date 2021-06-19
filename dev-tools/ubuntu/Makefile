DOCKER_IMAGE_TAG=0.0.1
DOCKER_IMAGE_URI=php-zookeeper/ubuntu-devel

.PHONY: build
build:
	docker build -t ${DOCKER_IMAGE_URI}:${DOCKER_IMAGE_TAG} .

push:
	docker push ${DOCKER_IMAGE_URI}:${DOCKER_IMAGE_TAG}

# vim: set noet:
