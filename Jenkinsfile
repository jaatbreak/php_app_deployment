pipeline {
    agent any
    
    environment {
        PROJECT_ID = 'playground-s-11-0252b97c'
        GOOGLE_CREDENTIALS = credentials('aman_service_account')
        REPOSITORY_NAME = 'gcr-node-app'
        GCR_URL = "gcr.io/${PROJECT_ID}/${REPOSITORY_NAME}"
        CLUSTER_NAME = 'cluster-1'
        CLUSTER_ZONE = 'us-central1'
       
    }
    
    stages {
        stage('SCM Checkout') {
            steps {
                git branch: 'main', url: 'https://github.com/jaatbreak/php_app_deployment.git'
            }
        }
        stage('Build Docker Image') {
            steps {
                script {
                    docker.build("${GCR_URL}:${BUILD_NUMBER}")
                }
            }
        }
        
        stage('Push to GCR') {
            steps {
                script {
                    docker.withRegistry('https://gcr.io', 'prod_gcp_cred') {
                        docker.image("${GCR_URL}:${BUILD_NUMBER}").push()
                    }
                }
            }
        }
        stage('Setup GKE Authentication') {
            steps {
                script {
                    // Authenticate with GCP using the service account key
                    withCredentials([file(credentialsId: 'aman_service_account', variable: 'GOOGLE_APPLICATION_CREDENTIALS')]) {
                        sh 'gcloud auth activate-service-account --key-file=$GOOGLE_APPLICATION_CREDENTIALS'
                    }
                    // Set the project and get cluster credentials
                    sh '''
                        gcloud config set project $PROJECT_ID
                        gcloud config set compute/zone $CLUSTER_ZONE
                        gcloud container clusters get-credentials $CLUSTER_NAME
                    '''
                }
            }
        }
        stage('change the image '){
            steps{
                sh 'kubectl set image deployment/myapp-deployment myapp-container=${GCR_URL}:${BUILD_NUMBER}'
            }
        }
    
    }
}
