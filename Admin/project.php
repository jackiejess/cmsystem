<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Project</title>
    <link rel="stylesheet" href="comp/css/project.css">
    <script>
        function addResourceField() {
            const container = document.getElementById('resourceFields');
            const resourceItem = document.createElement('div');
            resourceItem.classList.add('resource-item');
            resourceItem.innerHTML = `
                <div>
                    <label for="resourceType[]">Resource Type:</label>
                    <input type="text" name="resource_type[]" required>
                </div>
                <div>
                    <label for="resourceAmount[]">Amount Used:</label>
                    <input type="text" name="resource_amount[]" required>
                </div>
                <button type="button" class="delete-resource-btn" onclick="removeResourceField(this)">Delete</button>
            `;
            container.appendChild(resourceItem);
        }

        function removeResourceField(button) {
            const container = document.getElementById('resourceFields');
            container.removeChild(button.parentElement);
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Create Project</h1>
        <form action="project_add.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="projectName">Project Name:</label>
                <input type="text" id="projectName" name="projectName" required>
            </div>
            <div class="form-group">
                <label for="projectDescription">Project Description:</label>
                <textarea id="projectDescription" name="projectDescription" required></textarea>
            </div>
            <div class="form-group">
                <label for="resourceFields">Project Resources:</label>
                <div id="resourceFields">
                    <div class="resource-item">
                        <div>
                            <label for="resourceType[]">Resource Type:</label>
                            <input type="text" name="resource_type[]" required>
                        </div>
                        <div>
                            <label for="resourceAmount[]">Amount to be used:</label>
                            <input type="text" name="resource_amount[]" required>
                        </div>
                        <button type="button" class="delete-resource-btn" onclick="removeResourceField(this)">Delete</button>
                    </div>
                </div>
                <button type="button" onclick="addResourceField()">Add Resource</button>
            </div>
            <div class="form-group">
                <label for="projectBudget">Project Budget:</label>
                <input type="number" id="projectBudget" name="projectBudget" required>
            </div>
            <div class="form-group">
                <label for="projectImage">Project Image (Optional):</label>
                <input type="file" id="projectImage" name="projectImage" accept="image/*">
            </div>
            <button type="submit">Create Project</button>
        </form>
    </div>
</body>
</html>
