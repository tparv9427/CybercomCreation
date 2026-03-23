<template>
  <aside class="sidebar">
    <div class="logo-container">
      <div class="logo-icon">📊</div>
      <div class="logo-text">
        <div class="logo-title">ReportEngine</div>
        <div class="logo-sub">Solr Analytics</div>
      </div>
    </div>

    <nav class="nav-menu">
      <div class="nav-title">Main</div>
      <router-link to="/" class="nav-item" exact-active-class="active">
        <span class="icon">🏠</span>
        <span>Dashboard</span>
      </router-link>
      <router-link to="/reports" class="nav-item" active-class="active">
        <span class="icon">📋</span>
        <span>Reports &amp; Data</span>
      </router-link>

      <div class="nav-title" style="margin-top:1.5rem">Quick Actions</div>
      <router-link to="/reports" class="nav-item sub" active-class="active">
        <span class="icon">🔍</span>
        <span>Filter Builder</span>
      </router-link>
      <router-link to="/reports" class="nav-item sub" active-class="active">
        <span class="icon">📊</span>
        <span>Charts</span>
      </router-link>
      <router-link to="/reports" class="nav-item sub" active-class="active">
        <span class="icon">⇌</span>
        <span>Period Compare</span>
      </router-link>
      <router-link to="/reports" class="nav-item sub" active-class="active">
        <span class="icon">💾</span>
        <span>Saved Views</span>
      </router-link>
    </nav>

    <div class="sidebar-footer">
      <div class="solr-status">
        <span class="status-dot"></span>
        <span>Solr connected</span>
      </div>
    </div>
  </aside>
</template>

<style scoped>
.sidebar {
  width: 240px;
  background: linear-gradient(160deg, #4f46e5 0%, #3730a3 100%);
  color: white;
  display: flex;
  flex-direction: column;
  flex-shrink: 0;
}

.logo-container {
  height: 70px;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0 1.25rem;
  border-bottom: 1px solid rgba(255,255,255,0.1);
}

.logo-icon {
  font-size: 1.5rem;
  background: rgba(255,255,255,0.15);
  width: 38px;
  height: 38px;
  border-radius: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.logo-title {
  font-size: 0.95rem;
  font-weight: 700;
  letter-spacing: -0.3px;
  line-height: 1.2;
}

.logo-sub {
  font-size: 0.7rem;
  color: #a5b4fc;
  font-weight: 400;
}

.nav-menu {
  padding: 1.25rem 0.75rem;
  flex: 1;
  overflow-y: auto;
}

.nav-title {
  font-size: 0.65rem;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: #a5b4fc;
  margin-bottom: 0.5rem;
  padding-left: 0.5rem;
  font-weight: 700;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  padding: 0.65rem 0.875rem;
  border-radius: 0.5rem;
  color: #c7d2fe;
  font-size: 0.875rem;
  font-weight: 500;
  margin-bottom: 0.15rem;
  transition: all 0.15s;
}

.nav-item.sub {
  padding-left: 1.5rem;
  font-size: 0.825rem;
  color: #a5b4fc;
}

.nav-item:hover,
.nav-item.active {
  background: rgba(255,255,255,0.15);
  color: #fff;
}

.icon {
  font-size: 1rem;
  width: 20px;
  text-align: center;
  flex-shrink: 0;
}

.sidebar-footer {
  padding: 1rem 1.25rem;
  border-top: 1px solid rgba(255,255,255,0.1);
}

.solr-status {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.75rem;
  color: #a5b4fc;
}

.status-dot {
  width: 8px;
  height: 8px;
  background: #4ade80;
  border-radius: 50%;
  box-shadow: 0 0 6px #4ade80;
  flex-shrink: 0;
}
</style>
