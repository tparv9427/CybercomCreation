// app.js - Application bootstrap and event handling

const App = {
    currentEditingIntern: null,
    currentAssigningTask: null,

    // Initialize application
    init() {
        this.setupNavigation();
        this.setupModals();
        this.setupInternForm();
        this.setupTaskForm();
        this.setupFilters();
        this.setupErrorToast();
        this.setupLogs();
        this.setupThemeToggle();

        // Initial render
        Renderer.updateAllViews();

        console.log('Intern Operations System initialized');
    },

    // Setup navigation
    setupNavigation() {
        const navButtons = document.querySelectorAll('.nav-btn');
        navButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const view = e.target.dataset.view;
                this.navigateTo(view);
            });
        });
    },

    // Navigate to view
    navigateTo(viewName) {
        // Update nav buttons
        document.querySelectorAll('.nav-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.view === viewName) {
                btn.classList.add('active');
            }
        });

        // Update views
        document.querySelectorAll('.view').forEach(view => {
            view.classList.remove('active');
        });

        const targetView = document.getElementById(`${viewName}-view`);
        if (targetView) {
            targetView.classList.add('active');
            AppState.currentView = viewName;

            // Refresh data for the view
            if (viewName === 'dashboard') {
                Renderer.renderDashboard();
            } else if (viewName === 'interns') {
                Renderer.renderInternsTable();
            } else if (viewName === 'tasks') {
                Renderer.renderTasksTable();
            } else if (viewName === 'logs') {
                Renderer.renderLogs();
            }
        }
    },

    // Setup modals
    setupModals() {
        // Close modal buttons
        document.querySelectorAll('.close-modal, .cancel-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                this.closeAllModals();
            });
        });

        // Click outside to close
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    this.closeAllModals();
                }
            });
        });

        // Add intern button
        document.getElementById('add-intern-btn').addEventListener('click', () => {
            this.openInternModal();
        });

        // Add task button
        document.getElementById('add-task-btn').addEventListener('click', () => {
            this.openTaskModal();
        });
    },

    // Setup theme toggle
    setupThemeToggle() {
        const btn = document.getElementById('theme-toggle');

        // Set initial icon state based on AppState
        this.updateThemeIcons(AppState.darkMode);

        btn.addEventListener('click', () => {
            const isDark = AppState.toggleTheme();
            this.updateThemeIcons(isDark);
        });
    },

    updateThemeIcons(isDark) {
        const sun = document.querySelector('.sun-icon');
        const moon = document.querySelector('.moon-icon');
        if (isDark) {
            sun.classList.add('hidden');
            moon.classList.remove('hidden');
        } else {
            sun.classList.remove('hidden');
            moon.classList.add('hidden');
        }
    },

    // Setup intern form
    setupInternForm() {
        const form = document.getElementById('intern-form');
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            await this.handleInternSubmit();
        });
    },

    // Setup task form
    setupTaskForm() {
        const form = document.getElementById('task-form');
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            await this.handleTaskSubmit();
        });

        // Assign task confirmation
        document.getElementById('confirm-assign-btn').addEventListener('click', async () => {
            await this.handleTaskAssignment();
        });
    },

    // Setup filters
    setupFilters() {
        const statusFilter = document.getElementById('status-filter');
        const skillFilter = document.getElementById('skill-filter');
        const clearBtn = document.getElementById('clear-filters-btn');

        statusFilter.addEventListener('change', () => {
            AppState.filters.status = statusFilter.value;
            Renderer.renderInternsTable();
        });

        skillFilter.addEventListener('input', () => {
            AppState.filters.skills = skillFilter.value;
            Renderer.renderInternsTable();
        });

        clearBtn.addEventListener('click', () => {
            statusFilter.value = '';
            skillFilter.value = '';
            AppState.filters.status = '';
            AppState.filters.skills = '';
            Renderer.renderInternsTable();
        });
    },

    // Setup error toast
    setupErrorToast() {
        const closeBtn = document.querySelector('#error-toast .close-btn');
        closeBtn.addEventListener('click', () => {
            Renderer.hideError();
        });
    },

    // Setup logs
    setupLogs() {
        document.getElementById('clear-logs-btn').addEventListener('click', () => {
            this.showConfirm('Are you sure you want to clear all logs?', () => {
                AppState.clearLogs();
                Renderer.renderLogs();
            });
        });
    },

    // Custom Confirmation Modal
    showConfirm(message, onConfirm) {
        const modal = document.getElementById('confirmation-modal');
        const msgEl = document.getElementById('confirm-message');
        const yesBtn = document.getElementById('confirm-yes');
        const cancelBtn = document.getElementById('confirm-cancel');

        msgEl.textContent = message;
        modal.classList.remove('hidden');

        // Cleanup old listeners
        const newYes = yesBtn.cloneNode(true);
        const newCancel = cancelBtn.cloneNode(true);
        yesBtn.parentNode.replaceChild(newYes, yesBtn);
        cancelBtn.parentNode.replaceChild(newCancel, cancelBtn);

        newYes.addEventListener('click', () => {
            modal.classList.add('hidden');
            onConfirm();
        });

        newCancel.addEventListener('click', () => {
            modal.classList.add('hidden');
        });
    },

    // Open intern modal
    openInternModal(internId = null) {
        const modal = document.getElementById('intern-modal');
        const form = document.getElementById('intern-form');
        const title = document.getElementById('intern-modal-title');

        form.reset();
        Validators.clearFormErrors('intern-form');

        let initialSkills = [];

        if (internId) {
            const intern = AppState.getInternById(internId);
            if (intern) {
                title.textContent = 'Edit Intern';
                document.getElementById('intern-name').value = intern.name;
                document.getElementById('intern-email').value = intern.email;
                initialSkills = intern.skills || [];
                // Chip input handles value setting via Renderer setup
                this.currentEditingIntern = internId;
            }
        } else {
            title.textContent = 'Add Intern';
            this.currentEditingIntern = null;
        }

        // Initialize chips
        Renderer.setupChipsInput('intern-skills-container', initialSkills);

        modal.classList.remove('hidden');
    },

    // Open intern details modal
    openInternDetails(internId) {
        const modal = document.getElementById('intern-details-modal');
        Renderer.populateInternDetailsModal(internId);
        modal.classList.remove('hidden');
    },

    // Open task modal
    openTaskModal(taskId = null) {
        const modal = document.getElementById('task-modal');
        const form = document.getElementById('task-form');
        const title = document.getElementById('task-modal-title');

        form.reset();
        Validators.clearFormErrors('task-form');

        // For now, edit task isn't fully supported in UI buttons, but setup handles it
        title.textContent = 'Create Task';

        // Initialize chips
        Renderer.setupChipsInput('task-skills-container', []);

        modal.classList.remove('hidden');
    },

    // Open assign task modal
    openAssignTaskModal(taskId) {
        this.currentAssigningTask = taskId;
        const modal = document.getElementById('assign-task-modal');
        Renderer.populateAssignTaskModal(taskId);
        modal.classList.remove('hidden');
    },

    // Close all modals
    closeAllModals() {
        document.querySelectorAll('.modal').forEach(modal => {
            modal.classList.add('hidden');
        });
        this.currentEditingIntern = null;
        this.currentAssigningTask = null;
    },

    // Handle intern form submit
    async handleInternSubmit() {
        const skillsContainer = document.getElementById('intern-skills-container');
        const skills = skillsContainer.getSkills ? skillsContainer.getSkills() : [];

        const formData = {
            name: document.getElementById('intern-name').value,
            email: document.getElementById('intern-email').value,
            skills: skills
        };

        // Validate form
        const validation = Validators.validateInternForm(formData);
        if (!validation.valid) {
            Validators.displayFormErrors(validation.errors, 'intern-form');
            return;
        }

        try {
            Renderer.showLoading();

            if (this.currentEditingIntern) {
                await FakeServer.updateIntern(this.currentEditingIntern, validation.data);
            } else {
                await FakeServer.createIntern(validation.data);
            }

            this.closeAllModals();
            Renderer.updateAllViews();

        } catch (error) {
            Renderer.showError(error.message);
        } finally {
            Renderer.hideLoading();
        }
    },

    // Handle task form submit
    async handleTaskSubmit() {
        const skillsContainer = document.getElementById('task-skills-container');
        const skills = skillsContainer.getSkills ? skillsContainer.getSkills() : [];

        const formData = {
            title: document.getElementById('task-title').value,
            description: document.getElementById('task-description').value,
            requiredSkills: skills, // Pass array directly
            estimatedHours: document.getElementById('task-hours').value,
            dependencies: document.getElementById('task-dependencies').value
        };

        // Validate form
        // Need to update Validator because it expects comma-separated string for old flow
        // Or we convert array to it?
        // Let's rely on validator update. Wait, I didn't update Validator.
        // Quick fix: Validator expects string? Let's check Validator. If so, update Validator or adapter here.
        // Assuming Validator needs update because previously it parsed string. 
        // I should UPDATE Validators.js too to handle Array inputs or just skip validation of parsing?
        // Actually, validation usually happens ON THE PARSED DATA.
        // Let's pass the array and ensure Validator handles it.

        const validation = Validators.validateTaskForm(formData);
        if (!validation.valid) {
            Validators.displayFormErrors(validation.errors, 'task-form');
            return;
        }

        try {
            Renderer.showLoading();

            await FakeServer.createTask(validation.data);

            this.closeAllModals();
            Renderer.updateAllViews();

        } catch (error) {
            Renderer.showError(error.message);
        } finally {
            Renderer.hideLoading();
        }
    },

    // Handle task assignment
    async handleTaskAssignment() {
        const internId = document.getElementById('assign-intern-select').value;

        if (!internId) {
            Renderer.showError('Please select an intern');
            return;
        }

        try {
            Renderer.showLoading();

            await FakeServer.assignTaskToIntern(this.currentAssigningTask, internId);

            this.closeAllModals();
            Renderer.updateAllViews();

        } catch (error) {
            Renderer.showError(error.message);
        } finally {
            Renderer.hideLoading();
        }
    },

    // Activate intern
    async activateIntern(internId) {
        this.showConfirm('Activate this intern?', async () => {
            try {
                Renderer.showLoading();
                await FakeServer.changeInternStatus(internId, 'ACTIVE');
                // Refresh details modal if open
                if (!document.getElementById('intern-details-modal').classList.contains('hidden')) {
                    Renderer.populateInternDetailsModal(internId);
                    // Also refresh background views
                    Renderer.renderInternsTable();
                    Renderer.renderDashboard();
                } else {
                    Renderer.updateAllViews();
                }
            } catch (error) {
                Renderer.showError(error.message);
            } finally {
                Renderer.hideLoading();
            }
        });
    },

    // Exit intern
    async exitIntern(internId) {
        this.showConfirm('Mark this intern as exited?', async () => {
            try {
                Renderer.showLoading();
                await FakeServer.changeInternStatus(internId, 'EXITED');
                if (!document.getElementById('intern-details-modal').classList.contains('hidden')) {
                    Renderer.populateInternDetailsModal(internId);
                    Renderer.renderInternsTable();
                    Renderer.renderDashboard();
                } else {
                    Renderer.updateAllViews();
                }
            } catch (error) {
                Renderer.showError(error.message);
            } finally {
                Renderer.hideLoading();
            }
        });
    },

    // Restore intern
    async restoreIntern(internId) {
        this.showConfirm('Restore (re-hire) this intern?', async () => {
            try {
                Renderer.showLoading();
                // We use changeInternStatus, rules engine now allows EXITED -> ACTIVE
                await FakeServer.changeInternStatus(internId, 'ACTIVE');
                if (!document.getElementById('intern-details-modal').classList.contains('hidden')) {
                    Renderer.populateInternDetailsModal(internId);
                    Renderer.renderInternsTable();
                    Renderer.renderDashboard();
                } else {
                    Renderer.updateAllViews();
                }
            } catch (error) {
                Renderer.showError(error.message);
            } finally {
                Renderer.hideLoading();
            }
        });
    },

    // Delete intern
    async deleteIntern(internId) {
        const intern = AppState.getInternById(internId);
        this.showConfirm(`Delete intern ${intern.name}? This action cannot be undone.`, async () => {
            try {
                Renderer.showLoading();
                await FakeServer.deleteIntern(internId);
                this.closeAllModals(); // Close details modal
                Renderer.updateAllViews();
            } catch (error) {
                Renderer.showError(error.message);
            } finally {
                Renderer.hideLoading();
            }
        });
    },

    // Unassign task
    async unassignTask(taskId) {
        this.showConfirm('Unassign all interns from this task?', async () => {
            try {
                Renderer.showLoading();
                // Logic to unassign all? `AppState` has unassignTask which just nulls/clears.
                AppState.clearTaskAssignments(taskId); // We added this helper in state.js
                Renderer.updateAllViews();
            } catch (error) {
                Renderer.showError(error.message);
            } finally {
                Renderer.hideLoading();
            }
        });
    },

    // Complete task
    async completeTask(taskId) {
        this.showConfirm('Mark this task as complete?', async () => {
            try {
                Renderer.showLoading();
                await FakeServer.updateTaskStatus(taskId, 'DONE');
                Renderer.updateAllViews();
            } catch (error) {
                Renderer.showError(error.message);
            } finally {
                Renderer.hideLoading();
            }
        });
    },

    // Delete task
    async deleteTask(taskId) {
        const task = AppState.getTaskById(taskId);
        this.showConfirm(`Delete task "${task.title}"? This action cannot be undone.`, async () => {
            try {
                Renderer.showLoading();
                await FakeServer.deleteTask(taskId);
                Renderer.updateAllViews();
            } catch (error) {
                Renderer.showError(error.message);
            } finally {
                Renderer.hideLoading();
            }
        });
    }
};

// Initialize app when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    App.init();
});
