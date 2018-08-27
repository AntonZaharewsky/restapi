pipeline {
    agent any
    stages {
        stage('init') {
            steps {
                composer install
            }
        }

        stage('Check syntax') {
            steps {
                sh './vendor/bin/phpcs --standard=phpcs.xml ./'
            }
        }
    }
}