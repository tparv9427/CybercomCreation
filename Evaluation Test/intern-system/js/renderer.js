// renderer.js - DOM updates and rendering

const Renderer = {
    // Show loading overlay
    showLoading() {
        document.getElementById('loading-overlay').classList.remove('hidden');
    },

    // Hide loading overlay
    hideLoading() {
        document.getElementById('loading-overlay').classList.add('hidden');
    },

    // Show error toast
    showError(message) {
        const toast = document.getElementById('error-toast');
        const messageEl = toast.querySelector('.error-message');
        messageEl.textContent = message;
        toast.classList.remove('hidden');

        // Auto-hide after 5 seconds
        setTimeout(() => {
            toast.classList.add('hidden');
        }, 5000);
    },

    // Hide error toast
    hideError() {
        document.getElementById('error-toast').classList.add('hidden');
    },

    // Render dashboard
    renderDashboard() {
        const stats = AppState.getStats();

        // Update stats
        document.getElementById('total-interns').textContent = stats.totalInterns;
        document.getElementById('active-interns').textContent = stats.activeInterns;
        document.getElementById('total-tasks').textContent = stats.totalTasks;
        document.getElementById('pending-tasks').textContent = stats.pendingTasks;

        // Render recent interns
        this.renderRecentInterns();

        // Render recent tasks
        this.renderRecentTasks();
    },

    // Render recent interns
    renderRecentInterns() {
        const container = document.getElementById('recent-interns-list');
        const recentInterns = AppState.interns.slice(-5).reverse();

        if (recentInterns.length === 0) {
            container.innerHTML = '<p class="empty-state">No interns yet</p>';
            return;
        }

        container.innerHTML = recentInterns.map(intern => `
            <div class="recent-item">
                <div class="recent-item-info">
                    <h4>${this.escapeHtml(intern.name)}</h4>
                    <p>${this.escapeHtml(intern.email)} • ${this.getBadge(intern.status)}</p>
                </div>
            </div>
        `).join('');
    },

    // Render recent tasks
    renderRecentTasks() {
        const container = document.getElementById('recent-tasks-list');
        const recentTasks = AppState.tasks.slice(-5).reverse();

        if (recentTasks.length === 0) {
            container.innerHTML = '<p class="empty-state">No tasks yet</p>';
            return;
        }

        container.innerHTML = recentTasks.map(task => {
            return `
                <div class="recent-item">
                    <div class="recent-item-info">
                        <h4>${this.escapeHtml(task.title)}</h4>
                        <p>${task.assignedInterns && task.assignedInterns.length > 0 ? `${task.assignedInterns.length} assignee(s)` : 'Unassigned'} • ${task.estimatedHours}h</p>
                    </div>
                    <span class="badge badge-${task.status.toLowerCase().replace('_', '-')}">${task.status}</span>
                </div>
            `;
        }).join('');
    },

    // Render interns grid (New Minimal UI)
    renderInternsTable() {
        const grid = document.getElementById('interns-grid');
        const emptyState = document.getElementById('interns-empty-state');
        const interns = AppState.getFilteredInterns();

        if (interns.length === 0) {
            grid.innerHTML = '';
            emptyState.classList.remove('hidden');
            return;
        }

        emptyState.classList.add('hidden');

        grid.innerHTML = interns.map(intern => {
            const taskCount = AppState.getInternTaskCount(intern.id);
            const initials = intern.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();

            return `
                <div class="intern-card glass-panel card-3d animate-slide-up" onclick="App.openInternDetails('${intern.id}')">
                    <div class="intern-card-header">
                        <div class="intern-avatar">${initials}</div>
                        ${this.getBadge(intern.status)}
                    </div>
                    <div class="intern-info">
                        <h3>${this.escapeHtml(intern.name)}</h3>
                        <p class="intern-email">${this.escapeHtml(intern.email)}</p>
                    </div>
                    <div class="intern-stats">
                        <div class="stat-mini">
                            <span class="label">Tasks</span>
                            <span class="value">${taskCount}</span>
                        </div>
                        <div class="stat-mini">
                            <span class="label">Skills</span>
                            <span class="value">${intern.skills.length}</span>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    },

    // Populate Intern Details Modal
    populateInternDetailsModal(internId) {
        const intern = AppState.getInternById(internId);
        if (!intern) return;

        const body = document.getElementById('intern-details-body');
        const footer = document.querySelector('#intern-details-modal .modal-footer');

        // Find assigned tasks
        const assignedTasks = AppState.tasks.filter(t => t.assignedInterns && t.assignedInterns.includes(internId));

        const initials = intern.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();

        body.innerHTML = `
            <div class="details-header">
                <div class="large-avatar">${initials}</div>
                <div>
                    <h2>${this.escapeHtml(intern.name)}</h2>
                    <p class="text-muted">${this.escapeHtml(intern.email)}</p>
                    <div class="mt-2">${this.getBadge(intern.status)}</div>
                </div>
            </div>
            
            <div class="details-section">
                <h3>Skills</h3>
                <div class="skills-container">
                    ${intern.skills.map(skill => `<span class="skill-tag">${this.escapeHtml(skill)}</span>`).join('')}
                </div>
            </div>
            
            <div class="details-section">
                <h3>Assigned Tasks (${assignedTasks.length})</h3>
                ${assignedTasks.length > 0 ? `
                    <div class="tasks-list-mini">
                        ${assignedTasks.map(task => `
                            <div class="task-item-mini">
                                <span class="task-status-dot ${task.status.toLowerCase()}"></span>
                                <div>
                                    <strong>${this.escapeHtml(task.title)}</strong>
                                    <div class="text-sm text-muted">Due in ${task.estimatedHours}h</div>
                                </div>
                                <span class="badge badge-sm badge-${task.status.toLowerCase().replace('_', '-')}">${task.status}</span>
                            </div>
                        `).join('')}
                    </div>
                ` : '<p class="text-muted">No tasks currently assigned.</p>'}
            </div>
        `;

        // Actions
        let buttons = '';
        if (intern.status === 'ONBOARDING') {
            buttons += `<button class="btn btn-success" onclick="App.activateIntern('${intern.id}')">Activate</button>`;
            buttons += `<button class="btn btn-secondary" onclick="App.openInternModal('${intern.id}')">Edit</button>`;
        } else if (intern.status === 'ACTIVE') {
            buttons += `<button class="btn btn-secondary" onclick="App.openInternModal('${intern.id}')">Edit</button>`;
            buttons += `<button class="btn btn-warning" onclick="App.exitIntern('${intern.id}')">Exit</button>`;
        } else if (intern.status === 'EXITED') {
            buttons += `<button class="btn btn-success" onclick="App.restoreIntern('${intern.id}')">Restore (Re-hire)</button>`;
        }

        // Delete only if no tasks
        const taskCount = AppState.getInternTaskCount(intern.id); // This checks multi-assign logic in state now
        if (taskCount === 0) {
            buttons += `<button class="btn btn-danger" onclick="App.deleteIntern('${intern.id}')">Delete</button>`;
        }

        footer.innerHTML = buttons;
    },

    // Render tasks table
    renderTasksTable() {
        const tbody = document.getElementById('tasks-table-body');
        const tasks = AppState.tasks;

        if (tasks.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="empty-state">No tasks found</td></tr>';
            return;
        }

        tbody.innerHTML = tasks.map(task => {
            // Updated for multiple assignees
            let assignedDisplay = '<em>Unassigned</em>';
            if (task.assignedInterns && task.assignedInterns.length > 0) {
                assignedDisplay = `<div class="avatar-group">
                    ${task.assignedInterns.map(id => {
                    const i = AppState.getInternById(id);
                    if (!i) return '';
                    const init = i.name.split(' ').map(n => n[0]).join('').substring(0, 1);
                    return `<div class="avatar-mini" title="${this.escapeHtml(i.name)}">${init}</div>`;
                }).join('')}
                </div>`;
            }

            return `
                <tr>
                    <td><strong>${this.escapeHtml(task.id)}</strong></td>
                    <td>${this.escapeHtml(task.title)}</td>
                    <td>
                        <div class="skills-container">
                            ${task.requiredSkills.map(skill =>
                `<span class="skill-tag">${this.escapeHtml(skill)}</span>`
            ).join('')}
                        </div>
                    </td>
                    <td>${assignedDisplay}</td>
                    <td>
                        <span class="badge badge-${task.status.toLowerCase().replace('_', '-')}">${task.status}</span>
                    </td>
                    <td><strong>${task.estimatedHours}h</strong></td>
                    <td>
                        ${this.renderDependencies(task)}
                    </td>
                    <td>
                        <div class="action-btns">
                            ${this.getTaskActionButtons(task)}
                        </div>
                    </td>
                </tr>
            `;
        }).join('');

        document.getElementById('total-hours').textContent = AppState.getTotalEstimatedHours();
    },

    // Render task dependencies
    renderDependencies(task) {
        if (!task.dependencies || task.dependencies.length === 0) {
            return '<em>None</em>';
        }

        return `
            <div class="dependencies-list">
                ${task.dependencies.map(depId => {
            const depTask = AppState.getTaskById(depId);
            const isDone = depTask && depTask.status === 'DONE';
            return `<span class="dependency-tag ${isDone ? 'done' : ''}">${this.escapeHtml(depId)}</span>`;
        }).join('')}
            </div>
        `;
    },

    // Get action buttons for task
    getTaskActionButtons(task) {
        let buttons = '';

        if (task.status !== 'DONE') {
            buttons += `<button class="btn btn-info btn-sm" onclick="App.openAssignTaskModal('${task.id}')">Assign</button>`;
        }

        // Multi-assign unassign logic? 
        // Simple "Unassign" button handles removing all? Or opens modal?
        // Let's add a "Unassign" button if anyone is assigned.
        if (task.assignedInterns && task.assignedInterns.length > 0 && task.status !== 'DONE') {
            buttons += `<button class="btn btn-secondary btn-sm" onclick="App.unassignTask('${task.id}')">Unassign All</button>`;
        }

        if (task.status === 'IN_PROGRESS') {
            // Logic for canComplete
            const canComplete = RulesEngine.areDependenciesResolved(task);
            buttons += `<button class="btn btn-success btn-sm" ${!canComplete ? 'disabled' : ''} onclick="App.completeTask('${task.id}')">Complete</button>`;
        }

        const hasDependents = AppState.tasks.some(t =>
            t.dependencies && t.dependencies.includes(task.id)
        );

        if (!hasDependents) {
            buttons += `<button class="btn btn-danger btn-sm" onclick="App.deleteTask('${task.id}')">Delete</button>`;
        }

        return buttons;
    },

    // Render logs
    renderLogs() {
        const container = document.getElementById('logs-list');
        const logs = AppState.logs;

        if (logs.length === 0) {
            container.innerHTML = `
                <div class="empty-logs">
                    <p>No activity logs yet</p>
                </div>
            `;
            return;
        }

        container.innerHTML = logs.map(log => `
            <div class="log-entry ${log.type}">
                <div class="log-time">${this.formatDate(log.timestamp)}</div>
                <div class="log-action">${this.escapeHtml(log.action)}</div>
                <div class="log-details">${this.escapeHtml(log.details)}</div>
            </div>
        `).join('');
    },

    // Populate assign task modal
    populateAssignTaskModal(taskId) {
        const task = AppState.getTaskById(taskId);
        if (!task) return;

        const info = document.getElementById('assign-task-info');
        info.innerHTML = `Assigning <strong>${this.escapeHtml(task.title)}</strong><br>Required Skills: ${task.requiredSkills.join(', ')}`;

        const select = document.getElementById('assign-intern-select');
        const eligibleInterns = RulesEngine.getEligibleInterns(task);

        // Filter out interns already assigned to THIS task
        const availableInterns = eligibleInterns.filter(i =>
            !task.assignedInterns || !task.assignedInterns.includes(i.id)
        );

        if (availableInterns.length === 0) {
            select.innerHTML = '<option value="">No eligible interns available (all matched interns already assigned)</option>';
            document.getElementById('confirm-assign-btn').disabled = true;
            return;
        }

        select.innerHTML = '<option value="">-- Select an intern --</option>' +
            availableInterns.map(intern =>
                `<option value="${intern.id}">${this.escapeHtml(intern.name)} (${this.escapeHtml(intern.id)})</option>`
            ).join('');

        document.getElementById('confirm-assign-btn').disabled = false;
    },

    // Utility: Get badge HTML
    getBadge(value) {
        const className = `badge-${value.toLowerCase().replace('_', '-')}`;
        return `<span class="badge ${className}">${value}</span>`;
    },

    // Utility: Format date
    formatDate(isoString) {
        const date = new Date(isoString);
        return date.toLocaleString();
    },

    // Utility: Escape HTML
    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    },

    // Update all views
    updateAllViews() {
        this.renderDashboard();
        this.renderInternsTable();
        this.renderTasksTable();
        this.renderLogs();
    },

    // Setup chips inputs
    setupChipsInput(containerId, initialSkills = []) {
        const container = document.getElementById(containerId);
        const input = container.querySelector('input');
        const state = { value: [...initialSkills] }; // Closure state

        // Clear existing chips except input
        Array.from(container.children).forEach(child => {
            if (child !== input) container.removeChild(child);
        });

        const renderChips = () => {
            // Remove all chip elements
            Array.from(container.querySelectorAll('.chip')).forEach(c => c.remove());

            // Re-add in correct order before input
            state.value.forEach(skill => {
                const chip = document.createElement('div');
                chip.className = 'chip';
                chip.innerHTML = `<span>${this.escapeHtml(skill)}</span><span class="chip-remove">&times;</span>`;

                chip.querySelector('.chip-remove').addEventListener('click', () => {
                    state.value = state.value.filter(s => s !== skill);
                    renderChips();
                    updateHiddenValue();
                });

                container.insertBefore(chip, input);
            });
        };

        // Helper to update a hidden input or just expose getter
        // For simplicity, we'll attach the 'get skills' function to the DOM element
        container.getSkills = () => state.value;

        renderChips();

        // Event listeners
        // Remove old listeners to avoid dupes? Since we re-render whole view occasionally?
        // Actually renderer.js methods are called repeatedly. Setup should maybe be in App.js?
        // No, `renderInternsTable` is called repeatedly, but `setupChipsInput` is called once per Modal Open.

        const handleInput = (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                const val = input.value.trim();
                if (val && !state.value.includes(val)) {
                    state.value.push(val);
                    input.value = '';
                    renderChips();
                }
            } else if (e.key === 'Backspace' && input.value === '' && state.value.length > 0) {
                state.value.pop();
                renderChips();
            }
        };

        // Clone input to remove old listeners
        const newInput = input.cloneNode(true);
        input.parentNode.replaceChild(newInput, input);

        newInput.addEventListener('keydown', handleInput);

        // Handle blur to add valid input
        newInput.addEventListener('blur', () => {
            const val = newInput.value.trim();
            if (val && !state.value.includes(val)) {
                state.value.push(val);
                newInput.value = '';
                renderChips();
            }
        });

        return container;
    }
};
