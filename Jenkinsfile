pipeline {
    agent any
    stages {
        stage('init') {
            steps {
                sh 'composer install'
            }
        }

        stage('Check syntax') {
            steps {
                sh './vendor/bin/phpcs --standard=phpcs.xml ./'
            }
        }
    }
}