document.addEventListener('DOMContentLoaded', function () {
    const projectForm = document.getElementById('projectForm');
    const projectsContainer = document.getElementById('projectsContainer');
    let projects = [];

    projectForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const projectId = document.getElementById('projectId').value;
        const projectName = document.getElementById('projectName').value;
        const projectDescription = document.getElementById('projectDescription').value;
        const projectBudget = document.getElementById('projectBudget').value;

        if (projectId) {
            // Edit existing project
            const project = projects.find(p => p.id === projectId);
            project.name = projectName;
            project.description = projectDescription;
            project.budget = projectBudget;
        } else {
            // Add new project
            const newProject = {
                id: Date.now().toString(),
                name: projectName,
                description: projectDescription,
                budget: projectBudget
            };
            projects.push(newProject);
        }

        renderProjects();
        projectForm.reset();
        document.getElementById('projectId').value = '';
    });

    function renderProjects() {
        projectsContainer.innerHTML = '';
        projects.forEach(project => {
            const projectElement = document.createElement('div');
            projectElement.classList.add('project-item');
            projectElement.innerHTML = `
                <h3>${project.name}</h3>
                <p>${project.description}</p>
                <p><strong>Budget:</strong> $${project.budget}</p>
                <div class="project-actions">
                    <button onclick="editProject('${project.id}')">Edit</button>
                    <button onclick="deleteProject('${project.id}')">Delete</button>
                </div>
            `;
            projectsContainer.appendChild(projectElement);
        });
    }

    window.editProject = function (id) {
        const project = projects.find(p => p.id === id);
        document.getElementById('projectId').value = project.id;
        document.getElementById('projectName').value = project.name;
        document.getElementById('projectDescription').value = project.description;
        document.getElementById('projectBudget').value = project.budget;
    };

    window.deleteProject = function (id) {
        projects = projects.filter(p => p.id !== id);
        renderProjects();
    };
});
