<?php
// Sort brands alphabetically and group by letter
$groupedBrands = [];
foreach ($brands as $brand) {
    $firstLetter = strtoupper(substr($brand['name'], 0, 1));
    if (!preg_match('/[A-Z]/', $firstLetter)) {
        $firstLetter = '#';
    }
    $groupedBrands[$firstLetter][] = $brand;
}
ksort($groupedBrands);
if (isset($groupedBrands['#'])) {
    $hash = $groupedBrands['#'];
    unset($groupedBrands['#']);
    $groupedBrands['#'] = $hash;
}

// Popular brands (just take first few for demo, or we could have a 'popular' flag)
$popularBrands = array_slice($brands, 0, 8); 

// Currently selected brand IDs
$selectedBrandIds = isset($_GET['brand']) ? (array)$_GET['brand'] : [];
?>

<div id="brandModal" class="brand-popover">
    <div class="brand-popover-content glassmorphism">
        <div class="brand-popover-header">
            <div class="brand-search-container">
                <input type="text" id="brandSearch" placeholder="Search Brand" onkeyup="filterBrands()">
                <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </div>
            <button class="close-popover" onclick="closeBrandModal()">&times;</button>
        </div>
        
        <div class="brand-alphabet-nav">
            <a href="javascript:void(0)" onclick="scrollToLetter('#')">#</a>
            <?php foreach (range('A', 'Z') as $letter): ?>
                <a href="javascript:void(0)" onclick="scrollToLetter('<?php echo $letter; ?>')"><?php echo $letter; ?></a>
            <?php endforeach; ?>
        </div>

        <div class="brand-popover-body" id="brandModalBody">
            <!-- Popular Section -->
            <div class="brand-group popular-group">
                <h5 class="group-title">Popular</h5>
                <div class="brand-grid">
                    <?php foreach ($popularBrands as $brand): ?>
                        <label class="brand-checkbox-item">
                            <input type="checkbox" name="modal_brand[]" value="<?php echo $brand['id']; ?>" 
                                   <?php echo in_array($brand['id'], $selectedBrandIds) ? 'checked' : ''; ?>>
                            <span><?php echo $brand['name']; ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Grouped Brands -->
            <?php foreach ($groupedBrands as $letter => $letterBrands): ?>
                <div class="brand-group" id="group-<?php echo $letter; ?>">
                    <h5 class="group-title"><?php echo $letter; ?></h5>
                    <div class="brand-grid">
                        <?php foreach ($letterBrands as $brand): ?>
                            <label class="brand-checkbox-item brand-item-node" data-name="<?php echo strtolower($brand['name']); ?>">
                                <input type="checkbox" name="modal_brand[]" value="<?php echo $brand['id']; ?>"
                                       <?php echo in_array($brand['id'], $selectedBrandIds) ? 'checked' : ''; ?>>
                                <span><?php echo $brand['name']; ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="brand-popover-footer">
            <button class="btn-clear" onclick="clearAllBrands()">CLEAR ALL</button>
            <button class="btn-apply" onclick="applyBrandFilters()">APPLY FILTERS</button>
        </div>
    </div>
</div>

<style>
/* Brand Popover Styles */
.brand-popover {
    display: none;
    position: fixed;
    z-index: 9999;
    width: 750px;
    max-height: 500px;
}

body.no-scroll {
    overflow: hidden;
}

.brand-popover-content {
    width: 100%;
    height: 100%;
    background: var(--card-bg);
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
    border: 1px solid var(--border);
}

.brand-popover-header {
    padding: 0.8rem 1.2rem;
    border-bottom: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: var(--card-bg);
}

.brand-alphabet-nav {
    display: flex;
    flex-wrap: wrap;
    gap: 2px;
    padding: 0.4rem 1rem;
    background: var(--soft-bg);
    border-bottom: 1px solid var(--border);
    justify-content: center;
}

.brand-alphabet-nav a {
    text-decoration: none;
    color: var(--secondary);
    font-size: 0.7rem;
    font-weight: 700;
    width: 18px;
    height: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    transition: all 0.2s;
}

.brand-alphabet-nav a:hover {
    color: var(--accent);
    background: rgba(239, 131, 84, 0.1);
}

.brand-search-container {
    position: relative;
    flex: 1;
    max-width: 250px;
}

.brand-search-container input {
    width: 100%;
    padding: 0.4rem 1rem 0.4rem 2.2rem;
    border: 1px solid var(--border);
    border-radius: 6px;
    background: var(--soft-bg);
    color: var(--text);
    font-size: 0.8rem;
}

.close-popover {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: var(--secondary);
    cursor: pointer;
}

.brand-popover-body {
    flex: 1;
    overflow-y: auto;
    padding: 1.2rem;
    scroll-behavior: smooth;
    scrollbar-width: thin;
}

.brand-popover-body::-webkit-scrollbar {
    width: 6px;
}

.brand-popover-body::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 10px;
}

.brand-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.5rem;
}

.brand-checkbox-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.4rem;
    border-radius: 6px;
    cursor: pointer;
}

.brand-checkbox-item:hover {
    background: var(--soft-bg);
}

.brand-checkbox-item input {
    accent-color: var(--accent);
}

.brand-checkbox-item span {
    font-size: 0.85rem;
}

.brand-popover-footer {
    padding: 0.8rem 1rem;
    border-top: 1px solid var(--border);
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
}

.btn-clear {
    background: transparent;
    border: none;
    color: var(--accent);
    font-weight: 700;
    font-size: 0.8rem;
    cursor: pointer;
}

.btn-apply {
    background: var(--accent);
    color: white;
    border: none;
    border-radius: 6px;
    font-weight: 700;
    font-size: 0.8rem;
    cursor: pointer;
    padding: 0.5rem 1.2rem;
}
</style>

<script>
function openBrandModal(event) {
    event.stopPropagation();
    const popover = document.getElementById('brandModal');
    const button = event.currentTarget;
    const rect = button.getBoundingClientRect();
    const POPOVER_WIDTH = 750;
    const POPOVER_HEIGHT = 500;
    
    popover.style.display = 'block';
    document.body.classList.add('no-scroll');
    
    // Default position: below button
    let top = rect.bottom + 5;
    let left = rect.left;
    
    // Horizontal positioning (Stay within viewport)
    if (left + POPOVER_WIDTH > window.innerWidth) {
        left = window.innerWidth - POPOVER_WIDTH - 20;
    }
    left = Math.max(20, left);
    
    // Vertical positioning (Stay within viewport)
    // If popover goes off the bottom, try to flip it above the button first
    if (top + POPOVER_HEIGHT > window.innerHeight) {
        let flippedTop = rect.top - POPOVER_HEIGHT - 5;
        
        if (flippedTop > 20) {
            // Fits perfectly above the button
            top = flippedTop;
        } else {
            // Cannot fit below or above cleanly, just move it up from the bottom as much as possible
            top = window.innerHeight - POPOVER_HEIGHT - 20;
            // But don't let it go off the top of the screen
            top = Math.max(20, top);
        }
    }
    
    popover.style.top = top + 'px';
    popover.style.left = left + 'px';
    popover.style.height = POPOVER_HEIGHT + 'px';
}

function closeBrandModal() {
    document.getElementById('brandModal').style.display = 'none';
    document.body.classList.remove('no-scroll');
}

function scrollToLetter(letter) {
    const element = document.getElementById('group-' + letter);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function filterBrands() {
    const query = document.getElementById('brandSearch').value.toLowerCase();
    const items = document.querySelectorAll('.brand-item-node');
    items.forEach(item => {
        const name = item.getAttribute('data-name');
        item.style.display = name.includes(query) ? 'flex' : 'none';
    });
}

function clearAllBrands() {
    document.querySelectorAll('input[name="modal_brand[]"]').forEach(cb => cb.checked = false);
}

function applyBrandFilters() {
    const params = new URLSearchParams(window.location.search);
    params.delete('brand[]');
    document.querySelectorAll('input[name="modal_brand[]"]:checked').forEach(cb => {
        params.append('brand[]', cb.value);
    });
    // Remove no-scroll before redirect
    document.body.classList.remove('no-scroll');
    window.location.href = window.location.pathname + '?' + params.toString();
}

// Close popover when clicking outside
window.addEventListener('click', function(event) {
    const popover = document.getElementById('brandModal');
    if (popover && popover.style.display === 'block' && !popover.contains(event.target)) {
        closeBrandModal();
    }
});
</script>
