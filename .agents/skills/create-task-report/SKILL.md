---
name: create-task-report
description: "Creates a detailed report for a completed task, including modifications, testing, execution, and considerations for DevOps, infrastructure, and security."
---

## 1. Task Report Generation

This skill generates a detailed report for a completed task. The report is generated in Markdown format and includes the following sections:

- **Task Description:** A brief description of the task.
- **Modifications:** A detailed description of what was modified.
- **Testing:** Instructions on how to test the changes.
- **Local Execution:** Instructions on how to run the changes locally.
- **Production Execution:** Instructions on how to deploy and run the changes in a production environment.
- **Considerations:** Important points to consider about DevOps, infrastructure, and security.

## 2. Template

Use the following Markdown template to generate the report. Replace the bracketed placeholders with the relevant information for the completed task.

```markdown
# Task Report: [Task Name]

## 1. Task Description

[A brief description of the task.]

## 2. Modifications

[A detailed description of the changes made, including files, functions, and classes that were added, modified, or removed.]

## 3. Testing

[Instructions on how to test the changes. Include information about the testing environment, test data, and expected results.]

### 3.1. Unit Tests

[Instructions on how to run the unit tests.]

### 3.2. Integration Tests

[Instructions on how to run the integration tests.]

### 3.3. Manual Testing

[Step-by-step instructions for manual testing.]

## 4. Execution

### 4.1. Local Execution

[Instructions on how to run the changes locally. Include information about dependencies, environment variables, and any other configuration required.]

### 4.2. Production Execution

[Instructions on how to deploy and run the changes in a production environment. Include information about the deployment process, rollback procedures, and any post-deployment checks.]

## 5. Considerations

### 5.1. DevOps

[Important points to consider about DevOps, such as CI/CD pipeline changes, monitoring, and logging.]

### 5.2. Infrastructure

[Important points to consider about infrastructure, such as new services, changes to existing services, and cost implications.]

### 5.3. Security

[Important points to consider about security, such as new vulnerabilities, changes to authentication and authorization, and data privacy.]
```

## 3. Instructions

1.  Ask the user for the task name and a brief description of the task.
2.  Fill in the template with the information provided by the user and any other relevant information from the project context.
3.  Present the generated report to the user.
