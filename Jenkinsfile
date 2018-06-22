pipeline {
  agent any
  stages {
    stage('composer') {
      steps {
        script {
          withAnt(installation: 'ant') {
            sh "ant phpunit"
          }
        }

      }
    }
  }
}