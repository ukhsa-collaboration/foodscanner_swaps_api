terraform {
  required_version = ">= 0.12.2"

    backend "s3" {
        region         = "eu-west-2"
        bucket         = "phe-stg-fsswaps-eu-west-2-terraform-state"
        key            = "phe/stg/fsswaps/terraform.tfstate"
        dynamodb_table = "phe-stg-fsswaps-eu-west-2-terraform-state-lock"
        profile        = ""
        role_arn       = ""
        encrypt        = "true"
      }
}