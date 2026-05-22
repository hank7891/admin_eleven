/**
 * 後台 list 表頭點擊排序（純客戶端，只排當前頁可見列）
 *
 * 使用方式：
 *   <table class="admin-table">
 *     <thead>
 *       <tr>
 *         <th data-sortable="name">名稱</th>
 *         <th data-sortable="created_at" data-sort-type="date">建立時間</th>
 *       </tr>
 *     </thead>
 *     <tbody>
 *       <tr>
 *         <td>陳大文</td>
 *         <td data-sort-value="2026-05-18 11:30:00">2026/05/18 11:30</td>
 *       </tr>
 *     </tbody>
 *   </table>
 *
 * 排序鍵抽取：cell 有 `data-sort-value` 優先，否則 textContent.trim()
 * 排序類型：th 上 `data-sort-type="number|date|text"`，預設 text
 * empty / null 值統一排到尾（asc / desc 皆然）
 * 中文用 localeCompare('zh-TW') 排序
 */

export const setupTableSort = () => {
    const tables = document.querySelectorAll('table.admin-table');
    tables.forEach(initTable);
};

const initTable = (table) => {
    const sortableHeaders = table.querySelectorAll('thead th[data-sortable]');

    if (!sortableHeaders.length) {
        return;
    }

    sortableHeaders.forEach((th) => {
        th.addEventListener('click', () => handleHeaderClick(table, th));
    });
};

const handleHeaderClick = (table, th) => {
    const tbody = table.querySelector('tbody');

    if (!tbody || !tbody.children.length) {
        return;
    }

    // 決定下一個方向：active 欄 asc → desc → asc 循環；切到別欄一律從 asc 起
    const isActive = th.classList.contains('is-sort-asc') || th.classList.contains('is-sort-desc');
    const nextDirection = isActive && th.classList.contains('is-sort-asc') ? 'desc' : 'asc';

    // 清除同表所有欄位的 active class
    table.querySelectorAll('thead th[data-sortable]').forEach((header) => {
        header.classList.remove('is-sort-asc', 'is-sort-desc');
    });

    // 套用新方向
    th.classList.add(nextDirection === 'asc' ? 'is-sort-asc' : 'is-sort-desc');

    // 取得欄位 index 與排序類型
    const headerCells = Array.from(table.querySelectorAll('thead th'));
    const columnIndex = headerCells.indexOf(th);
    const sortType = th.dataset.sortType || 'text';

    sortRows(tbody, columnIndex, sortType, nextDirection);
};

const sortRows = (tbody, columnIndex, sortType, direction) => {
    const rows = Array.from(tbody.children).filter((node) => node.tagName === 'TR');

    rows.sort((a, b) => {
        const aValue = extractCellValue(a, columnIndex);
        const bValue = extractCellValue(b, columnIndex);

        // empty / null 一律排到尾，不受 asc / desc 影響
        const aEmpty = isEmpty(aValue);
        const bEmpty = isEmpty(bValue);
        if (aEmpty && bEmpty) return 0;
        if (aEmpty) return 1;
        if (bEmpty) return -1;

        const comparison = compareValues(aValue, bValue, sortType);
        return direction === 'asc' ? comparison : -comparison;
    });

    // DOM 移動 row（appendChild 會自動從原位置移出，保留 element reference 與 event listeners）
    rows.forEach((row) => tbody.appendChild(row));
};

const extractCellValue = (row, columnIndex) => {
    const cell = row.children[columnIndex];
    if (!cell) return '';

    // data-sort-value 優先，否則取 textContent
    if (cell.dataset.sortValue !== undefined) {
        return cell.dataset.sortValue.trim();
    }
    return cell.textContent.trim();
};

const isEmpty = (value) => {
    return value === null || value === undefined || value === '' || value === '--';
};

const compareValues = (a, b, sortType) => {
    if (sortType === 'number') {
        const aNum = parseFloat(a);
        const bNum = parseFloat(b);
        if (isNaN(aNum) && isNaN(bNum)) return 0;
        if (isNaN(aNum)) return 1;
        if (isNaN(bNum)) return -1;
        return aNum - bNum;
    }

    if (sortType === 'date') {
        const aTime = Date.parse(a);
        const bTime = Date.parse(b);
        if (isNaN(aTime) && isNaN(bTime)) return 0;
        if (isNaN(aTime)) return 1;
        if (isNaN(bTime)) return -1;
        return aTime - bTime;
    }

    // 預設 text — 中文友善排序
    return a.localeCompare(b, 'zh-TW', { numeric: true, sensitivity: 'base' });
};
