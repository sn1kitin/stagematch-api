const API = location.pathname.split('/demo/')[0];

function getToken() {
    return localStorage.getItem('token');
}

function getUser() {
    const user = localStorage.getItem('user');
    return user ? JSON.parse(user) : null;
}

function saveLogin(token, user) {
    localStorage.setItem('token', token);
    localStorage.setItem('user', JSON.stringify(user));
}

function clearLogin() {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
}

async function api(method, path, body = null) {
    const options = { method, headers: { 'Accept': 'application/json' } };

    if (getToken()) {
        options.headers['Authorization'] = 'Bearer ' + getToken();
    }

    if (body !== null) {
        options.headers['Content-Type'] = 'application/json';
        options.body = JSON.stringify(body);
    }

    const response = await fetch(API + '/' + path, options);
    const json = await response.json();

    if (json.status === 401 && !location.pathname.endsWith('login.html')) {
        clearLogin();
        location.href = 'login.html';
    }

    return json;
}

function errorText(json) {
    let text = json.error.message;

    if (json.error.details) {
        text += ' ' + Object.values(json.error.details).join(' ');
    }

    return text;
}

function startPage(role) {
    const user = getUser();

    if (!user) {
        location.href = 'login.html';
        return null;
    }

    if (role && user.role !== role) {
        location.href = homeFor(user);
        return null;
    }

    showNav(user);
    return user;
}

function homeFor(user) {
    return user.role === 'student' ? 'matches.html' : 'vacancies.html';
}

function showNav(user) {
    const nav = document.getElementById('nav');

    const links = user.role === 'student'
        ? { 'matches.html': 'Matches', 'skills.html': 'Mijn skills', 'my-applications.html': 'Mijn sollicitaties' }
        : { 'vacancies.html': 'Mijn vacatures', 'vacancy-form.html': 'Nieuwe vacature', 'applications.html': 'Sollicitaties', 'all-vacancies.html': 'Alle vacatures' };

    const page = location.pathname.split('/').pop();
    const html = Object.entries(links)
        .map(([href, text]) => `<a href="${href}" class="${href === page ? 'active' : ''}">${text}</a>`)
        .join('');

    nav.innerHTML = `
        <div class="nav-inner">
            <a class="logo" href="${homeFor(user)}">Stage<span>Match</span></a>
            <nav class="nav-links">${html}</nav>
            <div class="nav-user">
                <span><b>${esc(user.name)}</b> (${user.role === 'student' ? 'student' : 'bedrijf'})</span>
                <button id="logout" class="btn ghost small">Uitloggen</button>
            </div>
        </div>
    `;

    document.getElementById('logout').onclick = async () => {
        await api('POST', 'logout');
        clearLogin();
        location.href = 'login.html';
    };
}

function esc(text) {
    const div = document.createElement('div');
    div.textContent = text ?? '';
    return div.innerHTML;
}

function skillNames(skills) {
    return skills.length ? skills.map(s => esc(s.name)).join(', ') : '-';
}

const icons = {
    check: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.75" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>',
    plus: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.75" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>',
};

function chips(skills, type) {
    return `<div class="chips">${skills.map(s => `<span class="chip ${type}">${esc(s.name)}</span>`).join('')}</div>`;
}

function skillGroups(matched, missing, view = 'student') {
    const have = view === 'company' ? 'Heeft al:' : 'Heb je al:';
    const all = view === 'company' ? 'Heeft alle gevraagde skills' : 'Je hebt alle skills';
    let html = '';

    if (matched.length) {
        html += `<div class="group"><div class="group-label have">${icons.check}${have}</div>${chips(matched, 'have')}</div>`;
    }

    if (missing.length) {
        html += `<div class="group"><div class="group-label missing">${icons.plus}Mist nog:</div>${chips(missing, 'missing')}</div>`;
    } else {
        html += `<div class="group-label have">${icons.check}${all}</div>`;
    }

    return html;
}

function ring(pct, small = false) {
    const dash = (pct / 100 * 314.16).toFixed(2);
    const progress = pct > 0
        ? `<circle class="progress" cx="60" cy="60" r="50" fill="none" stroke="#c67139" stroke-width="14" stroke-linecap="round" stroke-dasharray="${dash} 314.16"/>`
        : '';

    return `
        <div class="ring ${small ? 'small' : ''}" role="img" aria-label="${pct}% match">
            <svg viewBox="0 0 120 120">
                <circle cx="60" cy="60" r="50" fill="none" stroke="#ffe1d0" stroke-width="14"/>
                ${progress}
            </svg>
            <div class="ring-label"><span class="ring-pct">${pct}%</span><span class="ring-word">match</span></div>
        </div>
    `;
}

const statusText = { pending: 'In behandeling', accepted: 'Aangenomen', rejected: 'Afgewezen' };

function statusBadge(status) {
    return `<span class="status ${status}">${statusText[status]}</span>`;
}

function formatDate(date) {
    return new Date(date.replace(' ', 'T')).toLocaleDateString('nl-NL', { day: 'numeric', month: 'long', year: 'numeric' });
}
