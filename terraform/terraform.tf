provider "aws" {
  access_key = var.AWS_ACCESS_KEY
  secret_key = var.AWS_SECRET_KEY
  region     = var.AWS_REGION
}

variable "AWS_ACCESS_KEY" {}
variable "AWS_SECRET_KEY" {}
variable "AWS_REGION" { }

variable environment {
  type = string
  default = "dev"
}

variable "Creator" {
  type = string
  default = "Terraform"
}

variable "project" {
  type = string
  default = "Food Scanner"
}

variable swaps_api_auth_token {
  type = string
  description = "A long random string that should be placed in X_AUTH_TOKEN header of API requests to the swaps API for authentication.'"
}

variable swaps_api_app_auth_token {
  type = string
  description = "A long random string that should be placed in X_APP_AUTH_TOKEN header of API requests to the swaps API, by the phone APP, for authentication.'"
}

variable "ssh_public_key" {
  type = string
  description = "The public key that corresponds to the private SSH key you would want to use to log into the EC2 instances with, should you need to. Should start like 'ssh-rsa AAAA...'"
}

variable "iam_ec2_key" {
  type = string
  description = "The AWS key for the IAM user that has permission to spawn and start/stop ec2 instances."
  default = 3306
}

variable "iam_ec2_secret" {
  type = string
  description = "The AWS secret for the IAM user that has permission to spawn ec2 instances."
  default = 3306
}

variable "iam_ecr_key" {
  type = string
  description = "The AWS key for the IAM user that has permission to login to the ECR e.g. 'aws ecr get-login-password'."
}

variable "iam_ecr_secret" {
  type = string
  description = "The AWS secret for the IAM user that has permission to login to the ECR e.g. 'aws ecr get-login-password'."
}

variable "vpc_id" {
  type = string
  description = "The id of the VPC you wish for the swaps API instances and load balancer deployed to."
}

variable "swaps_api_docker_registry" {
  type = string
  description = "The registry to pull the docker image from. E.g. xxxxxxxxxxxx.dkr.ecr.eu-west-2.amazonaws.com"
}

variable "swaps_api_docker_image_name" {
  type = string
  description = "The name of the image. This **should** include the registry. E.g. xxxxxxxxxxxx.dkr.ecr.eu-west-2.amazonaws.com/swaps-api"
}

variable "compute_docker_registry" {
  type = string
  description = "The registry to pull the docker image from. E.g. xxxxxxxxxxxx.dkr.ecr.eu-west-2.amazonaws.com"
}

variable "compute_docker_image_name" {
  type = string
  description = "The name of the image. This **should** include the registry. E.g. xxxxxxxxxxxx.dkr.ecr.eu-west-2.amazonaws.com/swaps-compute-engine"
}

variable "etl_database_host" {
  type = string
  description = "The IP address or hostname of the ETL database."
}

variable "etl_database_username" {
  type = string
  description = "The username to connect to the ETL database with (read-only)."
}

variable "etl_database_password" {
  type = string
  description = "The password to connect to the ETL database with. This is a really long string when using AWS ECR."
}

variable "etl_database_name" {
  type = string
  description = "The name of the ETL database."
}

variable "etl_database_port" {
  type = number
  description = "The connection port of the ETL database"
  default = 3306
}

variable "etl_database_table" {
  type = string
  description = "The table that conains the new food output. E.g. food_consolidated"
  default = "food_consolidated"
}

variable "swaps_database_host" {
  type = string
  description = "The IP address or hostname of the swaps database."
}

variable "swaps_database_username" {
  type = string
  description = "The username to connect to the swaps database with (read and write access)."
}

variable "swaps_database_password" {
  type = string
  description = "The password to connect to the swaps database with."
}

variable "swaps_database_name" {
  type = string
  description = "The name of the swaps database."
}

variable "swaps_database_port" {
  type = number
  description = "The connection port of the swaps database"
  default = 3306
}

variable "food_database_host" {
  type = string
  description = "The IP address or hostname of the original food database (CMS)."
}

variable "food_database_username" {
  type = string
  description = "The username to connect to the food database with (read-only access)."
}

variable "food_database_password" {
  type = string
  description = "The password to connect to the food database with."
}

variable "food_database_name" {
  type = string
  description = "The name of the food database."
}

variable "food_database_port" {
  type = number
  description = "The connection port of the food database"
  default = 3306
}

variable "food_database_table" {
  type = string
  description = "The table that contains the food information for the CMS. e.g. f_food"
  default = "f_food"
}

variable "lb_certificate_arn" {
  type = string
  description = "SSL certificate ARN for load balancer"
}

data "aws_vpc" "swaps_api_vpc" {
  id = var.vpc_id
}

//data "aws_subnet_ids" "default" {
//  vpc_id = data.aws_vpc.swaps_api_vpc.id
//}

data "aws_subnet_ids" "public" {
  filter {
    name   = "tag:Type"
    values = ["public"]
  }
//  vpc_id = module.vpc.vpc_id
  vpc_id = data.aws_vpc.swaps_api_vpc.id
}


data "aws_subnet_ids" "private" {
  filter {
    name   = "tag:Type"
    values = ["private"]
  }
   //vpc_id = module.vpc.vpc_id
  vpc_id = data.aws_vpc.swaps_api_vpc.id
}

data "aws_subnet_ids" "all" {
//  vpc_id = module.vpc.vpc_id
  vpc_id = data.aws_vpc.swaps_api_vpc.id
}

data "template_file" "compute_instance_user_data" {
  template = file("./compute-instance-cloud-init.yml")

  vars = {
    ssh_public_key = var.ssh_public_key
    iam_ecr_key = var.iam_ecr_key
    iam_ecr_secret = var.iam_ecr_secret
    
    docker_registry = var.compute_docker_registry
    docker_image_name = var.compute_docker_image_name

    swaps_database_host = var.swaps_database_host
    swaps_database_username = var.swaps_database_username
    swaps_database_password = var.swaps_database_password
    swaps_database_name = var.swaps_database_name
    swaps_database_port = var.swaps_database_port
    
    etl_database_host = var.etl_database_host
    etl_database_username = var.etl_database_username
    etl_database_password = var.etl_database_password
    etl_database_name = var.etl_database_name
    etl_database_port = var.etl_database_port
    etl_database_table = var.etl_database_table
  }
}


# Create security group for the cache-compute instance
resource "aws_security_group" "swaps_compute_engine" {
  name = "swaps-cache-calculator"
  vpc_id = data.aws_vpc.swaps_api_vpc.id
  ingress {
    from_port = 22
    to_port = 22
    protocol = "tcp"
    cidr_blocks = [
      data.aws_vpc.swaps_api_vpc.cidr_block]
  }
  # Allow the server to connect outwards. E.g. to apply updates etc.
  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = {
    Environment = var.environment
    Project = var.project
    Creator = var.Creator
  }
}


# Create the ubuntu 20.04 EC2 cache-compute engine resource 
resource "aws_instance" "swaps_compute_engine" {
  ami                    = "ami-05c424d59413a2876"
  instance_type          = "c5.24xlarge"
  vpc_security_group_ids = [aws_security_group.swaps_compute_engine.id]
  subnet_id = tolist(data.aws_subnet_ids.public.ids)[0]
  user_data = data.template_file.compute_instance_user_data.rendered
  associate_public_ip_address = true

  tags = {
    Name = "swaps-cache-calculator"
    Environment = var.environment
    Project = var.project
    Creator = var.Creator
  }
}


# Create the security group for the load balancer
resource "aws_security_group" "alb" {
  name = "swaps-api-alb"
  vpc_id = data.aws_vpc.swaps_api_vpc.id

  # Allow inbound HTTP requests
  ingress {
    from_port   = 80
    to_port     = 80
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }
  
  # Allow inbound HTTPS
  ingress {
    from_port   = 443
    to_port     = 443
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }
  
  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }
  tags = {
    Environment = var.environment
    Project = var.project
    Creator = var.Creator
  }
}


# Create security group for the swaps API instances
resource "aws_security_group" "instance" {
  name = "swaps-api"
  vpc_id = data.aws_vpc.swaps_api_vpc.id

//  ingress {
//    from_port   = 22
//    to_port     = 22
//    protocol    = "tcp"
//    cidr_blocks = ["0.0.0.0/0"]
//  }

  ingress {
    from_port   = 80
    to_port     = 80
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    from_port   = 443
    to_port     = 443
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  # Allow the server to connect outwards. E.g. to apply updates etc.
  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }
  tags = {
    Environment = var.environment
    Project = var.project
    Creator = var.Creator
  }
}


data "template_file" "user_data" {
  template = file("./swaps-api-cloud-init.yml")

  vars = {
    ssh_public_key = var.ssh_public_key
    iam_ecr_key = var.iam_ecr_key
    iam_ecr_secret = var.iam_ecr_secret
    iam_ec2_key = var.iam_ec2_key
    iam_ec2_secret = var.iam_ec2_secret
    swaps_api_auth_token = var.swaps_api_auth_token
    swaps_api_app_auth_token = var.swaps_api_app_auth_token
    docker_registry = var.swaps_api_docker_registry
    docker_image_name = var.swaps_api_docker_image_name

    swaps_database_host = var.swaps_database_host
    swaps_database_username = var.swaps_database_username
    swaps_database_password = var.swaps_database_password
    swaps_database_name = var.swaps_database_name
    swaps_database_port = var.swaps_database_port
    
    etl_database_host = var.etl_database_host
    etl_database_username = var.etl_database_username
    etl_database_password = var.etl_database_password
    etl_database_name = var.etl_database_name
    etl_database_port = var.etl_database_port
    etl_database_table = var.etl_database_table
    
    food_database_host = var.food_database_host
    food_database_username = var.food_database_username
    food_database_password = var.food_database_password
    food_database_name = var.food_database_name
    food_database_port = var.food_database_port
    food_database_table = var.food_database_table
    
    compute_instance_id = aws_instance.swaps_compute_engine.id
  }
}


# Create the launch configuration, which specifies what the auto scaling group (defined later) will
# launch
resource "aws_launch_configuration" "swaps_api" {
  image_id        = "ami-05c424d59413a2876"
  instance_type   = "t3a.micro"
  security_groups = [aws_security_group.instance.id]
  user_data       = data.template_file.user_data.rendered
  associate_public_ip_address = true
  # When swapping out instances, launch new ones before destroying old ones so there is no 
  # downtime.
  lifecycle {
    create_before_destroy = true
  }
}


# Create the auto scaling group (ASG) that will manage the deployed EC2 instances.
resource "aws_autoscaling_group" "swaps_api_asg" {
  launch_configuration = aws_launch_configuration.swaps_api.name
//  vpc_zone_identifier  = data.aws_subnet_ids.default.ids
  vpc_zone_identifier  = data.aws_subnet_ids.public.ids
  name = "fs_swaps_asg"
  target_group_arns = [aws_lb_target_group.asg.arn]
  health_check_type = "ELB"

  min_size = 2
  max_size = 10

  tag {
    key                 = "Name"
    value               = "swaps-api-asg"
    propagate_at_launch = true
  }
}


# Create the load balancer
resource "aws_lb" "swaps_api_alb" {
  name               = "swaps-api-load-balancer"
  load_balancer_type = "application"
//  subnets            = data.aws_subnet_ids.default.ids
  subnets            = data.aws_subnet_ids.public.ids
  security_groups    = [aws_security_group.alb.id]

  tags = {
    Environment = var.environment
    Project = var.project
    Creator = var.Creator
  }
}


# Create a listener for the load balancer we just created.
//resource "aws_lb_listener" "http" {
//  load_balancer_arn = aws_lb.swaps_api_alb.arn
//  port              = 80
//  protocol          = "HTTP"
//
//  # By default, return a simple 404 page
//  default_action {
//    type = "fixed-response"
//
//    fixed_response {
//      content_type = "text/plain"
//      message_body = "404: page not found"
//      status_code  = 404
//    }
//  }
//}
//

resource "aws_lb_listener" "http" {
  load_balancer_arn = aws_lb.swaps_api_alb.arn
  port              = 80
  protocol          = "HTTP"

  default_action {
    type = "redirect"

    redirect {
      port        = "443"
      protocol    = "HTTPS"
      status_code = "HTTP_301"
    }
  }
}

resource "aws_lb_listener" "https" {
  load_balancer_arn = aws_lb.swaps_api_alb.arn
  port              = 443
  protocol          = "HTTPS"
  ssl_policy        = "ELBSecurityPolicy-2016-08"
  certificate_arn   =  var.lb_certificate_arn

  # By default, return a simple 404 page
  default_action {
    type             = "forward"
    target_group_arn = aws_lb_target_group.asg.arn
  }
}

# Create a target group that will health check against our EC2 API instances.
resource "aws_lb_target_group" "asg" {
  name     = "swaps-api-asg"
  port     = 80
  protocol = "HTTP"
//  vpc_id   = module.vpc.vpc_id
  vpc_id = data.aws_vpc.swaps_api_vpc.id

  health_check {
    path                = "/api/ping"
    protocol            = "HTTP"
    matcher             = "200"
    interval            = 15
    timeout             = 3
    healthy_threshold   = 2
    unhealthy_threshold = 2
  }
}



# Add a rule to the load balancer listener, monitoring our 
//resource "aws_lb_listener_rule" "asg" {
//  listener_arn = aws_lb_listener.http.arn
//  priority     = 100
//
//  condition {
//    path_pattern {
//      values = ["*"]
//    }
//  }
//
//  action {
//    type             = "forward"
//    target_group_arn = aws_lb_target_group.asg.arn
//  }
//}


output "alb_dns_name" {
  value       = aws_lb.swaps_api_alb.dns_name
  description = "The domain name of the load balancer"
}


output "asg_name" {
  value       = aws_autoscaling_group.swaps_api_asg.name
  description = "The name of the Auto Scaling Group"
}
