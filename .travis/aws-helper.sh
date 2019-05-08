#!/usr/bin/env bash

set -o errexit

echo "Logging in to AWS Docker Repository"
$(aws ecr get-login --no-include-email --region $AWS_DEFAULT_REGION)

function build_JOPLIN {
	JOPLIN_IMAGE=$1
	JOPLIN_MODE=":latest"
	JOPLIN_FILE="Dockerfile"

	# First, determine if we are building the base image.
	if [ "${JOPLIN_IMAGE}" = "base" ]; then
		JOPLIN_MODE=":base"
		JOPLIN_FILE="${JOPLIN_FILE}.base"
	fi;

	# Then, generate the tag string to use in the AWS private registry
	JOPLIN_TAG="cityofaustin/JOPLIN-api${JOPLIN_MODE}"
	echo "Final Image Name (tag): ${JOPLIN_TAG}"

	# Build
  echo "Building ${JOPLIN_FILE} with tag ${JOPLIN_TAG}"
	docker build -f $JOPLIN_FILE -t $JOPLIN_TAG .

	# Tag
  echo "Tagging ${JOPLIN_TAG} with tag ${JOPLIN_TAG}"
	docker tag $JOPLIN_TAG $JOPLIN_REPO/$JOPLIN_TAG

	# Push
  echo "Pushing: $JOPLIN_REPO/$JOPLIN_TAG"
  docker push $JOPLIN_REPO/$JOPLIN_TAG

  echo "Done"
}

#
# Restarts all tasks (containers) in ECS, this will cause them to relaunch
# with the latest version of the code and/or latest version of the database.
#
function restart_all_tasks {
  echo "Updating ECS Cluster"
  aws ecs update-service --force-new-deployment --cluster $JOPLIN_CLUSTER --service $JOPLIN_SERVICE

  echo "Stopping any old remaining containers (autoscaling should spawn new tasks)"
  for JOPLIN_TASK_ID in $(aws ecs list-tasks --cluster $JOPLIN_CLUSTER | jq -r ".taskArns[] | split(\"/\") | .[1]");
  do
	echo "Stopping task id: ${JOPLIN_TASK_ID}";
	aws ecs stop-task --cluster $JOPLIN_CLUSTER --task $JOPLIN_TASK_ID;
  done
}
