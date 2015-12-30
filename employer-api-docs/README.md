# Vietnamworks’s API documentation

This repository contains the documentation for [Vietnamworks](http://www.vietnamworks.com)’s API.

#### Contents

- [Overview](#1-overview)
- [Authentication](#2-authentication)
  - [Browser-based authentication](#21-browser-based-authentication)
- [Resources](#3-resources)
  - [Job form structure](#31-job-form-structure)
  - [Job posting](#32-job-posting)
  - [Job editing](#33-job-editing)
- [Testing](#4-testing)

## 1. Overview

Vietnamworks’s API is a JSON-based OAuth2 API. All requests are made to endpoints beginning:
`https://api.vietnamworks.com/api/rest/v1`

All requests must be secure, i.e. `https`, not `http`.

#### Developer agreement

By using Vietnamworks’s API, you agree to our [terms of service](http://employer.vietnamworks.com/terms_of_use.php).

## 2. Authentication

In order to publish on behalf of a Vietnamworks Employer account, you will need an access token. An access token grants limited access to a user’s account. We offer to acquire an access token by browser-based OAuth authentication.


### 2.1. Browser-based authentication

To be able to use these APIs you will need a consumer key which you can apply for it by filling [Vietnamworks feedback form](http://www.vietnamworks.com/contact-us/feedback) and remember to choose API Consumer Key as your topic and mention your company name as well as requesting to access Employer API. 
Then we will supply you a `clientId` and and a `clientSecret` with which you may access Vietnamworks’s API. Each integration should have its own `clientId` and `clientSecret`. The `clientSecret` should be treated like a password and stored securely.

The first step is to acquire a short term authorization code by sending the user to our authorization URL so they can grant access to your integration.

```
https://api.vietnamworks.com/oauth/v2/auth?client_id={{clientId}}
    &scope=jobpost
    &state={{state}}
    &response_type=code
    &redirect_uri={{redirectUri}}
```
Example:
```
https://api.vietnamworks.com/oauth/v2/auth?client_id=8_5utlrcculw8wss0gskg0sok0goksggoc8sk8soowwk84scc8wk
    &scope=jobpost&state=qazwsxedcrfv
    &response_type=code&redirect_uri=http://vietnamworks.local
```
With the following parameters:

| Parameter       | Type     | Required?  | Description                                     |
| -------------   |----------|------------|-------------------------------------------------|
| `client_id`     | string   | required   | The clientId we will supply you that identifies your integration. |
| `scope`         | string   | required   | The access that your integration is requesting, comma separated. Currently, there are two valid scope values, which are listed below. Most integrations should request `jobview` `jobpost` |
| `state`         | string   | required   | Arbitrary text of your choosing, which we will repeat back to you to help you prevent request forgery. |
| `response_type` | string   | required   | The field currently has only one valid value, and should be `code`.  |
| `redirect_uri`  | string   | required   | The URL where we will send the user after they have completed the login dialog. This must exactly match one of the callback URLs you provided when creating your app. This field should be URL encoded. |

The following scope values are valid:

| Scope              | Description                                                             | Extended |
| -------------------| ----------------------------------------------------------------------- | -------- |
| jobview       | Grants basic access to a approval job posting’s information related to the employer.    | No       |
| jobpost       | Grants the ability to publish a job posting to Vietnamworks website    | No       |

Integrations are not permitted to request extended scope from users without explicit prior permission from Vietnamworks. Attempting to request these permissions through the standard user authentication flow will result in an error if extended scope has not been authorized for an integration.

![alt tag](http://farm2.staticflickr.com/1545/23813550532_3ace3780e4_b.jpg)
![alt tag](http://farm6.staticflickr.com/5635/23554127839_083929f6eb_b.jpg)

If the user grants your request for access, we will send them back to the specified `redirect_uri` with a state and code parameter:


```
{{redirectUri}}?state={{state}}
    &code={{code}}
```
Example:
```
http://vietnamworks.local/?state=qazwsxedcrfv&code=MzAwZDJjNGNhNDdhMzQ4NTY1MDMwNmExNmU5NWUzYTFjZmFhODUyNjc3MTQ1ODExZjMxZTVmOTg0M2NmMjZiMQ
```

With the following parameters:

| Parameter       | Type     | Required?  | Description                                     |
| -------------   |----------|------------|-------------------------------------------------|
| `state`         | string   | optional   | The state you specified in the request.         |
| `code`          | string   | required   | A short-lived authorization code that may be exchanged for an access token. |

If the user declines access, we will send them back to the specified `redirect_uri` with an error parameter:

```
https://example.com/callback?error=access_denied
```

Once you have an authorization code, you may exchange it for a long-lived access token with which you can make authenticated requests on behalf of the user. To acquire an access token, make a form-encoded server-side GET request:

```
GET /oauth/v2/token HTTP/1.1
Host: api.vietnamworks.com
Content-Type: application/x-www-form-urlencoded
Accept: application/json
Accept-Charset: utf-8

code={{code}}&client_id={{client_id}}&client_secret={{client_secret}}&grant_type=authorization_code&redirect_uri={{redirect_uri}}
```
Example
```
https://api.vietnamworks.com/oauth/v2/token?client_id=8_5utlrcculw8wss0gskg0sok0goksggoc8sk8soowwk84scc8wk
    &client_secret=4ubvz8qf68sgkgowgcg0g84o48oo44oogo48gkw08k4okg4wo4
    &grant_type=authorization_code
    &redirect_uri=http://vietnamworks.local
    &code=MzAwZDJjNGNhNDdhMzQ4NTY1MDMwNmExNmU5NWUzYTFjZmFhODUyNjc3MTQ1ODExZjMxZTVmOTg0M2NmMjZiMQ
```

With the following parameters:

| Parameter       | Type     | Required?  | Description                                     |
| -------------   |----------|------------|-------------------------------------------------|
| `code`          | string   | required   | The authorization code you received in the previous step. |
| `client_id`     | string   | required   | Your integration’s `clientId` |
| `client_secret` | string   | required   | Your integration’s `clientSecret` |
| `grant_type`    | string   | required   | The literal string "authorization_code" |
| `redirect_uri`  | string   | required   | The same redirect_uri you specified when requesting an authorization code. |

If successful, you will receive back an access token response:

```
HTTP/1.1 201 OK
Content-Type: application/json; charset=utf-8
{
    access_token: "MTFmMTY2MTI2ZGQ1NGRmZDljZGFiZGQ2YzVjNGIyMGI5NTY0NDQ0MDI3M2EyMjIyNWM5ZmZiM2FmMjRhNDljMA",
    expires_in: 2592000,
    token_type: "bearer",
    scope: "jobpost",
    refresh_token: "ZWMzYTNjYjMzZDkzOTYyNTEyNTUwZjdmMzMwOTIwM2RlOGU4MTgxNTNiNDMwNDg4OWY1ZGJmYzkwNDVhMGM3NA"
}
```

With the following parameters:

| Parameter       | Type         | Required?  | Description                                     |
| -------------   |--------------|------------|-------------------------------------------------|
| `token_type`    | string       | required   | The literal string "Bearer"                     |
| `access_token`  | string       | required   | A token that is valid for 30 days and may be used to perform authenticated requests on behalf of the user. |
| `refresh_token` | string       | required   | A token that does not expire which may be used to acquire a new `access_token`.                            |
| `scope`         | string array | required   | The scopes granted to your integration.         |
| `expires_in`    | int64        | required   | The timestamp in unix time when the access token will expire |

Each access token is valid for 30 days. When an access token expires, you may request a new token using the refresh token. Refresh tokens do not expire. Both access tokens and refresh tokens may be revoked by the user at any time. **You must treat both access tokens and refresh tokens like passwords and store them securely.**

Access token and refresh token like this:

```
MTFmMTY2MTI2ZGQ1NGRmZDljZGFiZGQ2YzVjNGIyMGI5NTY0NDQ0MDI3M2EyMjIyNWM5ZmZiM2FmMjRhNDljMA
```

To acquire a new access token using a refresh token, make the following form-encoded request:

```
POST /oauth/v2/token HTTP/1.1
Host: api.vietnamworks.com
Content-Type: application/x-www-form-urlencoded
Accept: application/json
Accept-Charset: utf-8

refresh_token={{refresh_token}}&client_id={{client_id}}
&client_secret={{client_secret}}&grant_type=refresh_token
```

With the following parameters:

| Parameter       | Type     | Required?  | Description                                     |
| -------------   |----------|------------|-------------------------------------------------|
| `refresh_token` | string   | required   | A valid refresh token.                          |
| `client_id`     | string   | required   | Your integration’s `clientId`                   |
| `client_secret` | string   | required   | Your integration’s `clientSecret`               |
| `grant_type`    | string   | required   | The literal string "refresh_token"              |


## 3. Resources

The API is RESTful and arranged around resources. All requests must be made with an integration token. All requests must be made using `https`.

### 3.1. Job Form Structure

#### Getting the job form’s details
Returns details of the job posting form that employer has granted permission to publish job posting service to Vietnamworks website.

```
GET https://api.vietnamworks.com/api/rest/v1/jobs/new.json
```

Example request:

```
GET api/rest/v1/jobs/new.json HTTP/1.1
Host: api.vietnamworks.com
Authorization: Bearer MTFmMTY2MTI2ZGQ1NGRmZDljZGFiZGQ2YzVjNGIyMGI5NTY0NDQ0MDI3M2EyMjIyNWM5ZmZiM2FmMjRhNDljMA
Content-Type: application/json
Accept: application/json
Accept-Charset: utf-8
```

The response is a Job Form object within a Job Posting Purchase Order data.

Example response:

```
HTTP/1.1 200 OK
Content-Type: application/json; charset=utf-8

{
  "code": 200,
  "form_view": {
     "job": {
		"job_title": {
           "name": "job_title",
           "required": true,
           "type": "text",
           "expanded": false,
           "multiple": false,
           "readonly": false,
           "max_length": 100
        },
        "job_level": {
           "name": "job_level",
           "required": true,
           "type": "choice",
           "expanded": false,
           "multiple": false,
           "values": [
          	  {
              "label": "New Grad/Entry Level/Internship",
              "value": "1",
              "data": 1
              },
            ...
          ],
          "readonly": false
        },
        ...
    }
  }
}
```

Where a Job Form object is:

| Field      | Type   | Required   | Max Length |Description                               |
| -----------|--------|--------|--------|----------------------------------------------|
| job_title  | text | true | 100 | The title of job posting.|
| job_level  | choice | true |  | The level of job posting.                  |
| job_categories| choice | true |  | Industries of job posting. Choose maximum 3 industries|
| job_category_orders  | text | true  |  | The list of industries separated by commas in order of display|
| job_locations| choice | true |  | Locations of job posting. Choose maximum 3 cities|
| report_to| text | true |  | This job position will report to|
| minimum_salary| text | true |  | Salary range from|
| maximum_salary| text | true |  | Salary range to|
| is_show_salary| radio  | true |  | Allow to show or not salary range on Vietnamworks website|
| job_description | textarea  | true| 14500 | The short description job posting. |
| job_requirements| textarea | true| 14500 | The job posting requirements. |
| skill_tag1 | text  | false | 100 | Skill requirement for job posting position. |
| skill_tag2 | text  | false | 100 | Skill requirement for job posting position. |
| skill_tag3 | text  | false | 100 | Skill requirement for job posting position. |
| company_name| text | true| 255 | The employer’s company name on Vietnamworks. |
| company_size | choice | true|  | Number of employee in employer company   |
| company_address | text | true|  | The employer company’s address. |
| company_profile | textarea | true| 10000 | Employer company information|
| company_benefit1 | benefit  | false |  | benefit_id choice, benefit_description text to show what is benefit comapany provide|
| company_benefit2 | benefit  | false |  | benefit_id choice, benefit_description text to show what is benefit comapany provide|
| company_benefit3 | benefit  | false |  | benefit_id choice, benefit_description text to show what is benefit comapany provide|
| contact_name | text | true| 30 | The HR person’s name handle this job posting|
| is_show_contact | checkbox | false |  | Allow to show or not the HR person’s info handle this job posting|
| email_for_application | text | true| 255 | The email to recive job applications|
| preferred_language  | choice | true|  | The resume's language that employer prefer when job-seeker apply|
| job_posting_service  | choice  | true|  | The job posting service that employer purchase on Vietnamworks|

Possible errors:

| Error code           | Description                                     |
| ---------------------|-------------------------------------------------|
| 400 Bad request      | There is no available job posting service.      |
| 401 Unauthorized     | The `accessToken` is invalid or has been revoked. |

### 3.2. Job Posting

#### Listing the employer’s jobs posting

Returns a full list of approved job posting that the employer. This endpoint offers a set of data similar to what you will see at http://employer.vietnamworks.com/beta/job/default/index when logged in.

The REST API endpoint exposes this list of approved job posting as a collection of resources under the employer. A request to fetch a list of approved job posting for a employer looks like this:

```
GET https://api.vietnamworks.com/api/rest/v1/jobs/online.json
```

The response is a list of approved job posting objects. An empty array is returned if employer doesn’t have relations to any approved job posting. The response array is wrapped in a data envelope.

Example response:

```
HTTP/1.1 200 OK
Content-Type: application/json; charset=utf-8

{
  "code": 200,
  "jobs": {
     "data": [
        {
           "job_title": "Sales Executive - Japanese Business Unit",
           "job_description": "Develop an extensive customer database, especially Japanese clients.",
           "job_requirements": "Good written and spoken English is Essential.",
           "company_name": "Vietnamworks",
           "company_size_id": 4,
           "company_profile": "VietnamWorks is Vietnam's #1 online service for professionals looking for jobs and employers looking for talent.",
           "email_for_application": "son.tran@navigosgroup.com",
           "contact_name": "Tran Thai Son",
           "company_address": "130 Suong Nguyet Anh",
           "created_date": "2015-12-09T14:32:56+07:00",
           "duration_days": 30,
           "expired_date": "2016-01-15T23:59:59+07:00",
           "approved_date": "2015-12-16T17:06:46+07:00",
           "last_updated_date": "2015-12-16T17:06:46+07:00",
           "company_id": 201114,
           "num_of_views": 0,
           ...
           "is_show_contact": true,
           "unformatted_job_title": "Sales Executive - Japanese Business Unit",
           "alias": "sales-executive-japanese-business-unit-19",
           "unformatted_company_name": "Vietnamworks",
           "id": 561451
        }
      ]
  }
}
```

Where a Job Posting object is:

| Field       | Type   | Description                                     |
| ------------|--------|-------------------------------------------------|
| id          | string | A unique identifier for the job posting.        |
| job_title   | string | The job posting’s title on Vietnamworks.        |
| job_description | string | Short description of the job posting.           |
| job_requirements | string | The job posting requirements.           |
| company_name | string | The employer’s company name on Vietnamworks. |
| company_size_id | number | Number of employee in employer company. |
| company_profile | string | Employer company information.           |
| email_for_application | string | The email to recive job applications.           |
| contact_name | string | The HR person’s name handle this job posting.|
| company_address | string | The employer company’s address.           |
| created_date | timestamp | Created date of job posting.           |
| duration_days | number | Dureation days to show job posting on job-seeker site.           |
| expired_date | timestamp | Expired date of job posting.           |
| approved_date | timestamp | Approved date of job posting.           |
| last_updated_date | timestamp | Lastest update date of job posting.           |
| company_id | string | A unique identifier for the company.           |
| num_of_views | number | Number of views from job-seeker           |
| preferred_language_id | string | The resume's language id that employer prefer when job-seeker apply.           |
| salary_max | number | Salary range to |
| salary_min | number | Salary range from |
| job_level_id | number | The level id of job posting.           |
| is_show_contact | boolean | Allow to show or not the HR person’s info handle this job posting.           |
| unformatted_job_title | string | The job posting’s title provide by employer.           |
| alias | string | A unique identifier is generated by job title for the job posting.           |
| unformatted_company_name | string | The company name provide by employer.|
| num_of_applications | number | Number of applicatios from job-seeker           |
| job_locations | array | Array of locations of job posting |
| job_categories | array | Array of industries of job posting |
| company_benefits | array | Array of benefits of company |
| skill_tags | array | Array of skill tags of job posting |

Possible errors:

| Error code           | Description                                                                           |
| ---------------------|---------------------------------------------------------------------------------------|
| 401 Unauthorized     | The `accessToken` is invalid, lacks the `listJobPosting` scope or has been revoked. |
| 401 Forbidden        | The request attempts to list publications for another user.                           |

#### Jobs posting detail

Returns a full of approved job posting that the employer. This endpoint offers a set of data similar to what you’ll see at http://employer.vietnamworks.com/beta/job-posting/edit-job/{jobId} when logged in.

The REST API endpoint exposes this approved job posting as a resources under the employer. A request to fetch a approved job posting for a employer looks like this:

```
GET https://api.vietnamworks.com/api/rest/v1/jobs/{jobId}.json
```

The response is a approved job posting objects. An empty array is returned if employer doesn’t have relations to any approved job posting. The response array is wrapped in a data envelope.

Example response:

```
HTTP/1.1 200 OK
Content-Type: application/json; charset=utf-8
{
  code: 200,
  job: {
      "job_title": "Sales Executive - Japanese Business Unit",
      "salary_range_id": 0,
      "job_description": "Develop an extensive customer database, especially Japanese clients.",
      "job_requirements": "Good written and spoken English is Essential.",
      "company_name": "Vietnamworks",
      "company_size_id": 4,
      "company_profile": "VietnamWorks is Vietnam's #1 online service for professionals looking for jobs and employers looking for talent.",
      "email_for_application": "son.tran@navigosgroup.com",
      "contact_name": "Tran Thai Son",
      "company_address": "130 Suong Nguyet Anh",
      "created_date": "2015-12-09T14:32:56+07:00",
      "duration_days": 30,
      "expired_date": "2016-01-15T23:59:59+07:00",
      "approved_date": "2015-12-16T17:06:46+07:00",
      "last_updated_date": "2015-12-16T17:06:46+07:00",
      ...
      "company_id": 201114,
      "num_of_views": 0,
      "preferred_language_id": 2,
      "is_show_logo": 0,
      "salary_max": 1000,
      "salary_min": 700,
      "job_level_id": 5,
      "is_show_contact": true,
      "unformatted_job_title": "Sales Executive - Japanese Business Unit",
      "alias": "sales-executive-japanese-business-unit-19",
      "unformatted_company_name": "Vietnamworks",
      "id": 561451
  }
}
```

Where a Job Posting object is:

| Field       | Type   | Description                                     |
| ------------|--------|-------------------------------------------------|
| id          | string | A unique identifier for the job posting.        |
| job_title   | string | The job posting’s title on Vietnamworks.        |
| job_description | string | Short description of the job posting.           |
| job_requirements | string | The job posting requirements.           |
| company_name | string | The employer’s company name on Vietnamworks. |
| company_size_id | string | Number of employee in employer company. |
| company_profile | string | Employer company information.           |
| email_for_application | string | The email to recive job applications.           |
| contact_name | string | The HR person’s name handle this job posting.|
| company_address | string | The employer company’s address.           |
| created_date | string | Created date of job posting.           |
| duration_days | string | Dureation days to show job posting on job-seeker site.           |
| expired_date | timestamp | Expired date of job posting.           |
| approved_date | timestamp | Approved date of job posting.           |
| last_updated_date | timestamp | Lastest update date of job posting.           |
| company_id | string | A unique identifier for the company.           |
| num_of_views | string | Number of views from job-seeker           |
| preferred_language_id | string | The resume's language id that employer prefer when job-seeker apply.           |
| salary_max | string | Salary range to |
| salary_min | string | Salary range from |
| job_level_id | string | The level id of job posting.           |
| is_show_contact | string | Allow to show or not the HR person’s info handle this job posting.           |
| unformatted_job_title | string | The job posting’s title provide by employer.           |
| alias | string | A unique identifier is generated by job title for the job posting.           |
| unformatted_company_name | string | The company name provide by employer.|
| num_of_applications | number | Number of applicatios from job-seeker           |
| job_locations | array | Array of locations of job posting |
| job_categories | array | Array of industries of job posting |
| company_benefits | array | Array of benefits of company |
| skill_tags | array | Array of skill tags of job posting |

Possible errors:

| Error code           | Description                                                                           |
| ---------------------|---------------------------------------------------------------------------------------|
| 401 Unauthorized     | The `accessToken` is invalid. |
| 403 Forbidden        | Lacks the `jobview` scope or has been revoked. |
| 404 Not Found        | The `jobId` is invalid or not yours job. |

### 3.3. Posts

#### Creating a job posting
Creates a job posting on the authenticated user’s profile.

```
POST https://api.vietnamworks.com/api/rest/v1/jobs.json 
```

Example request:

```
POST /api/rest/v1/jobs.json HTTP/1.1
Host: api.vietnamworks.com
Authorization: Bearer MTFmMTY2MTI2ZGQ1NGRmZDljZGFiZGQ2YzVjNGIyMGI5NTY0NDQ0MDI3M2EyMjIyNWM5ZmZiM2FmMjRhNDljMA
Content-Type: application/json
Accept: application/json
Accept-Charset: utf-8
{
    "job": {
        "job_title": "Fresher Software Test Engineer",
        "job_level": 5,
        "job_categories": [
            35,
            70
        ],
        "job_category_orders": "35,70",
        "job_locations": [
            29,
            24
        ],
        "report_to": "IT Manager",
        "minimum_salary": 700,
        "maximum_salary": 1000,
        "is_show_salary": 1,
        "job_description": "Analyze system and software requirements",
        "job_requirements": "Bachelor degree or above in Electrical Engineering or equivalent",
        "skill_tag1": "English Advanced",
        "skill_tag2": "Embedded - C/C++ ",
        "skill_tag3": "QA/QC",
        "company_name": "VietnamWorks",
        "company_size": 4,
        "company_address": "10th Floor, Golden Tower, 6 Nguyen Thi Minh Khai, District 1, HCM City.",
        "company_profile": "VietnamWorks is Vietnam's #1 online service for professionals looking for jobs and employers looking for talent.",
        "company_benefit1": {
            "benefit_id": 1,
            "benefit_desc": "12 days annual leave"
        },
        "contact_name": "HR Department",
        "is_show_contact": 1,
        "email_for_application": "lan.bui@navigosgroup.com",
        "preferred_language": 2,
        "job_posting_service": 123
    }
}
```

With the following fields:

| Parameter       | Type         | Required?  | Description                                     |
| -------------   |--------------|------------|-------------------------------------------------|
| job_title           | string       | required   | The title of the job posting.|
| job_level   | integer       | required   | The job level of the job posting |
| job_categories | integer array | required   | industries of the job posting. At least one industry and maximum is 3 industries.  |
| job_category_orders  | integer array | required  | The order of `job_categories` list |
| job_locations | integer array | required   | working cities of the job posting. At least one city and maximum is 3 cities.  |
| report_to | string | required | Position will report to |
| minimum_salary | integer | required | Salary range from, which is greater than 1 and lower than `maximum_salary` |
| maximum_salary | integer | required | Salary range to, which is greater than 1 and greater than `maximum_salary` |
| is_show_salary | integer | required | Allow to show or not salary range on Vietnamworks website. The collection is [0,1] |
| job_description | string | required | Short description of the job posting. |
| job_requirements | string | required | The job posting requirements. |
| skill_tag1 | string | optional | Skill requirement for job posting position. Enter at least one skill tag on 3 parameter `skill_tag1` `skill_tag2` `skill_tag3`. |
| skill_tag2 | string | optional | Skill requirement for job posting position. Enter at least one skill tag on 3 parameter `skill_tag1` `skill_tag2` `skill_tag3`. |
| skill_tag3 | string | optional | Skill requirement for job posting position. Enter at least one skill tag on 3 parameter `skill_tag1` `skill_tag2` `skill_tag3`. |
| company_name | string | required | The employer’s company name on Vietnamworks. |
| company_size | string | required | Number of employee in employer company |
| company_address | string | required | The employer company’s address |
| company_profile | string | required | Employer company information. |
| company_benefit1 | benefit | required | benefit_id choice, benefit_description text to show what is benefit comapany provide |
| company_benefit2 | benefit | required | benefit_id choice, benefit_description text to show what is benefit comapany provide |
| company_benefit3 | benefit | required | benefit_id choice, benefit_description text to show what is benefit comapany provide |
| is_show_contact | string | required | Allow to show or not the HR person’s info handle this job posting |
| email_for_application | string | required | The email to recive job applications. |
| preferred_language | string | required | The resume's language that employer prefer when job-seeker apply |
| job_posting_service | string | required | The job posting service id that employer purchase on Vietnamworks |

The response is a location header that points to the URL of the new created job. Example response:

```
HTTP/1.1 201 Created
Content-Type: application/json; charset=utf-8
Location: /api/rest/v1/jobs/578189
```

Possible errors:

| Error code           | Description                                                                                                          |
| ---------------------|----------------------------------------------------------------------------------------------------------------------|
| 400 Bad Request      | Required fields were invalid, not specified. Or there is no available job posting service..                                                                          |
| 401 Unauthorized     | The access token is invalid or has been revoked.                                                                     |
| 403 Forbidden        | The user does not have permission to publish. |
| 404 Not Found        | The `jobId` is invalid or not yours job. |

### 3.4. Edit

#### Update online job posting information
Update a online job posting information on the authenticated user’s profile.

```
PUT https://api.vietnamworks.com/api/rest/v1/jobs/{jobId}.json 
```

Example request:

```
PUT /api/rest/v1/jobs.json HTTP/1.1
Host: api.vietnamworks.com
Authorization: Bearer MTFmMTY2MTI2ZGQ1NGRmZDljZGFiZGQ2YzVjNGIyMGI5NTY0NDQ0MDI3M2EyMjIyNWM5ZmZiM2FmMjRhNDljMA
Content-Type: application/json
Accept: application/json
Accept-Charset: utf-8
{
    "job": {
        "job_title": "Fresher Software Test Engineer",
        "job_level": 5,
        "job_categories": [
            35,
            70
        ],
        "job_category_orders": "35,70",
        "job_locations": [
            29,
            24
        ],
        "report_to": "IT Manager",
        "minimum_salary": 700,
        "maximum_salary": 1000,
        "is_show_salary": 1,
        "job_description": "Analyze system and software requirements",
        "job_requirements": "Bachelor degree or above in Electrical Engineering or equivalent",
        "skill_tag1": "English Advanced",
        "skill_tag2": "Embedded - C/C++ ",
        "skill_tag3": "QA/QC",
        "company_name": "VietnamWorks",
        "company_size": 4,
        "company_address": "10th Floor, Golden Tower, 6 Nguyen Thi Minh Khai, District 1, HCM City.",
        "company_profile": "VietnamWorks is Vietnam's #1 online service for professionals looking for jobs and employers looking for talent.",
        "company_benefit1": {
            "benefit_id": 1,
            "benefit_desc": "12 days annual leave"
        },
        "contact_name": "HR Department",
        "is_show_contact": 1,
        "email_for_application": "lan.bui@navigosgroup.com",
        "preferred_language": 2
    }
}
```

With the following fields:

| Parameter       | Type         | Required?  | Description                                     |
| -------------   |--------------|------------|-------------------------------------------------|
| job_title           | string       | required   | The title of the job posting.|
| job_level   | integer       | required   | The job level of the job posting |
| job_categories | integer array | required   | industries of the job posting. At least one industry and maximum is 3 industries.  |
| job_category_orders  | integer array | required  | The order of `job_categories` list |
| job_locations | integer array | required   | working cities of the job posting. At least one city and maximum is 3 cities.  |
| report_to | string | required | Position will report to |
| minimum_salary | integer | required | Salary range from, which is greater than 1 and lower than `maximum_salary` |
| maximum_salary | integer | required | Salary range to, which is greater than 1 and greater than `maximum_salary` |
| is_show_salary | integer | required | Allow to show or not salary range on Vietnamworks website. The collection is [0,1] |
| job_description | string | required | Short description of the job posting. |
| job_requirements | string | required | The job posting requirements. |
| skill_tag1 | string | optional | Skill requirement for job posting position. Enter at least one skill tag on 3 parameter `skill_tag1` `skill_tag2` `skill_tag3`. |
| skill_tag2 | string | optional | Skill requirement for job posting position. Enter at least one skill tag on 3 parameter `skill_tag1` `skill_tag2` `skill_tag3`. |
| skill_tag3 | string | optional | Skill requirement for job posting position. Enter at least one skill tag on 3 parameter `skill_tag1` `skill_tag2` `skill_tag3`. |
| company_name | string | required | The employer’s company name on Vietnamworks. |
| company_size | string | required | Number of employee in employer company |
| company_address | string | required | The employer company’s address |
| company_profile | string | required | Employer company information. |
| company_benefit1 | benefit | required | benefit_id choice, benefit_description text to show what is benefit comapany provide |
| company_benefit2 | benefit | required | benefit_id choice, benefit_description text to show what is benefit comapany provide |
| company_benefit3 | benefit | required | benefit_id choice, benefit_description text to show what is benefit comapany provide |
| is_show_contact | string | required | Allow to show or not the HR person’s info handle this job posting |
| email_for_application | string | required | The email to recive job applications. |
| preferred_language | string | required | The resume's language that employer prefer when job-seeker apply |
| job_posting_service | string | required | The job posting service id that employer purchase on Vietnamworks |

The response won't be returning a body. Example response:

```
HTTP/1.1 204 No Content
Content-Type: application/json; charset=utf-8
```

#### Update partial online job posting information
Update a online job posting information on the authenticated user’s profile.

```
PATCH https://api.vietnamworks.com/api/rest/v1/jobs/{jobId}.json 
```

Example request:

```
PATCH /api/rest/v1/jobs.json HTTP/1.1
Host: api.vietnamworks.com
Authorization: Bearer MTFmMTY2MTI2ZGQ1NGRmZDljZGFiZGQ2YzVjNGIyMGI5NTY0NDQ0MDI3M2EyMjIyNWM5ZmZiM2FmMjRhNDljMA
Content-Type: application/json
Accept: application/json
Accept-Charset: utf-8
{
    "job": {
        "job_title": "Fresher Software Test Engineer",
        "job_level": 5,
        "job_categories": [
            35,
            70
        ],
        "job_category_orders": "35,70",
        "job_locations": [
            29,
            24
        ],
        "report_to": "IT Manager",
        "minimum_salary": 700,
        "maximum_salary": 1000,
        "is_show_salary": 1,
        "job_description": "Analyze system and software requirements",
        "job_requirements": "Bachelor degree or above in Electrical Engineering or equivalent",
        "skill_tag1": "English Advanced",
        "skill_tag2": "Embedded - C/C++ ",
        "skill_tag3": "QA/QC",
        "company_name": "VietnamWorks",
        "company_size": 4,
        "company_address": "10th Floor, Golden Tower, 6 Nguyen Thi Minh Khai, District 1, HCM City.",
        "company_profile": "VietnamWorks is Vietnam's #1 online service for professionals looking for jobs and employers looking for talent.",
        "company_benefit1": {
            "benefit_id": 1,
            "benefit_desc": "12 days annual leave"
        },
        "contact_name": "HR Department",
        "is_show_contact": 1,
        "email_for_application": "lan.bui@navigosgroup.com",
        "preferred_language": 2,
        "job_posting_service": 123
    }
}
```

With the following fields:

| Parameter       | Type         | Required?  | Description                                     |
| -------------   |--------------|------------|-------------------------------------------------|
| job_title           | string       | required   | The title of the job posting.|
| job_level   | integer       | required   | The job level of the job posting |
| job_categories | integer array | required   | industries of the job posting. At least one industry and maximum is 3 industries.  |
| job_category_orders  | integer array | required  | The order of `job_categories` list |
| job_locations | integer array | required   | working cities of the job posting. At least one city and maximum is 3 cities.  |
| report_to | string | required | Position will report to |
| minimum_salary | integer | required | Salary range from, which is greater than 1 and lower than `maximum_salary` |
| maximum_salary | integer | required | Salary range to, which is greater than 1 and greater than `maximum_salary` |
| is_show_salary | integer | required | Allow to show or not salary range on Vietnamworks website. The collection is [0,1] |
| job_description | string | required | Short description of the job posting. |
| job_requirements | string | required | The job posting requirements. |
| skill_tag1 | string | optional | Skill requirement for job posting position. Enter at least one skill tag on 3 parameter `skill_tag1` `skill_tag2` `skill_tag3`. |
| skill_tag2 | string | optional | Skill requirement for job posting position. Enter at least one skill tag on 3 parameter `skill_tag1` `skill_tag2` `skill_tag3`. |
| skill_tag3 | string | optional | Skill requirement for job posting position. Enter at least one skill tag on 3 parameter `skill_tag1` `skill_tag2` `skill_tag3`. |
| company_name | string | required | The employer’s company name on Vietnamworks. |
| company_size | string | required | Number of employee in employer company |
| company_address | string | required | The employer company’s address |
| company_profile | string | required | Employer company information. |
| company_benefit1 | benefit | required | benefit_id choice, benefit_description text to show what is benefit comapany provide |
| company_benefit2 | benefit | required | benefit_id choice, benefit_description text to show what is benefit comapany provide |
| company_benefit3 | benefit | required | benefit_id choice, benefit_description text to show what is benefit comapany provide |
| is_show_contact | string | required | Allow to show or not the HR person’s info handle this job posting |
| email_for_application | string | required | The email to recive job applications. |
| preferred_language | string | required | The resume's language that employer prefer when job-seeker apply |
| job_posting_service | string | required | The job posting service id that employer purchase on Vietnamworks |

The response won't be returning a body. Example response:

```
HTTP/1.1 204 No Content
Content-Type: application/json; charset=utf-8
```

Possible errors:

| Error code           | Description                                                                                                          |
| ---------------------|----------------------------------------------------------------------------------------------------------------------|
| 400 Bad Request      | Required fields were invalid, not specified.                                                                         |
| 401 Unauthorized     | The access token is invalid or has been revoked.                                                                     |
| 403 Forbidden        | The user does not have permission to publish. |
| 404 Not Found        | The `jobId` is invalid or not yours job. |


## 4. Testing

We have a sandbox environment for testing. All requests are made to endpoints beginning:

`https://api-staging.vietnamworks.com/api/rest/v1`

Please feel free contact us to create a testing account.