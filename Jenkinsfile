pipeline {
  agent any
  stages {
    stage('composer') {
      steps {
        sh 'composer install'
      }
    }
    stage('tests') {
      steps {
        script {
          withAnt(installation: 'ant') {
            sh "ant phpunit"
          }
        }

      }
    }
  }
  post {
    always {
      ciGame()
    }
  }
}
