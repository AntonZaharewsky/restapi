pipeline {
    agent any
    stages {
        stage('init') {
            steps {
                composer install
            }
        }

        stage('Stage 1') {
            steps {
                echo 'Hello world!'
            }
        }

        stage('Check syntax') {
            steps {
                ./vendor/bin/phpcs --standard=phpcs.xml ./
            }
        }
    }
}