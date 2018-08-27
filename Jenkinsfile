pipeline {
    agent any
    stages {
        stage('init') {
                composer install
        }

        stage('Check syntax') {
                sh './vendor/bin/phpcs --standard=phpcs.xml ./'
        }
    }
}