<?php
// Initialize variables
$note_content = '';
$note_title = '';
$status_message = '';
$theme = 'light';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Save note content
    if (isset($_POST['save_note'])) {
        $note_content = $_POST['note_content'];
        $note_title = $_POST['note_title'];
        
        // Save to file if title is provided
        if (!empty($note_title)) {
            $filename = 'notes/' . preg_replace('/[^a-zA-Z0-9_-]/', '', $note_title) . '.txt';
            
            // Create directory if it doesn't exist
            if (!is_dir('notes')) {
                mkdir('notes', 0755, true);
            }
            
            if (file_put_contents($filename, $note_content)) {
                $status_message = 'Note saved successfully!';
            } else {
                $status_message = 'Error saving note. Please try again.';
            }
        } else {
            $status_message = 'Please provide a title to save your note.';
        }
    }
    
    // Load note content
    if (isset($_POST['load_note'])) {
        $note_title = $_POST['note_title'];
        
        if (!empty($note_title)) {
            $filename = 'notes/' . preg_replace('/[^a-zA-Z0-9_-]/', '', $note_title) . '.txt';
            
            if (file_exists($filename)) {
                $note_content = file_get_contents($filename);
                $status_message = 'Note loaded successfully!';
            } else {
                $status_message = 'Note not found. Create a new one.';
            }
        } else {
            $status_message = 'Please provide a title to load a note.';
        }
    }
    
    // Download note as text file
    if (isset($_POST['download'])) {
        $note_content = $_POST['note_content'];
        $note_title = !empty($_POST['note_title']) ? $_POST['note_title'] : 'untitled_note';
        
        // Set headers for download
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="' . $note_title . '.txt"');
        echo $note_content;
        exit;
    }
    
    // Clear note content
    if (isset($_POST['clear'])) {
        $note_content = '';
        $status_message = 'Note cleared!';
    }
    
    // Toggle theme
    if (isset($_POST['toggle_theme'])) {
        $theme = ($_POST['current_theme'] === 'light') ? 'dark' : 'light';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Notepad - Your Notes Anywhere</title>
    
    <!-- Meta tags for SEO -->
    <meta name="description" content="Free online notepad app with text formatting, saving, and downloading features. Take notes anywhere without installing any software.">
    <meta name="keywords" content="online notepad, free notepad, text editor, notes, web notepad, online text editor">
    <meta name="author" content="Online Notepad">
    
    <!-- Open Graph tags for social sharing -->
    <meta property="og:title" content="Online Notepad - Take Notes Anywhere">
    <meta property="og:description" content="Free online notepad app with saving and downloading features. No installation required.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://yourdomain.com/notepad">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #2ecc71;
            --bg-color: #f8f9fa;
            --text-color: #333;
            --border-color: #dee2e6;
        }
        
        body.dark-theme {
            --primary-color: #375a7f;
            --secondary-color: #3498db;
            --success-color: #00bc8c;
            --bg-color: #222;
            --text-color: #eee;
            --border-color: #444;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: background-color 0.3s, color 0.3s;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .navbar-brand {
            font-weight: bold;
            color: var(--primary-color);
        }
        
        .navbar {
            background-color: var(--bg-color);
            border-bottom: 1px solid var(--border-color);
        }
        
        #editor-container {
            flex-grow: 1;
        }
        
        #note-content {
            resize: none;
            min-height: 300px;
            font-size: 1rem;
            line-height: 1.5;
            border: 1px solid var(--border-color);
            background-color: var(--bg-color);
            color: var(--text-color);
        }
        
        #note-content:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        
        .navbar .btn {
            margin-left: 5px;
        }
        
        .toolbar {
            padding: 10px;
            background-color: var(--bg-color);
            border: 1px solid var(--border-color);
            border-bottom: none;
            border-radius: 4px 4px 0 0;
        }
        
        .toolbar button {
            margin-right: 5px;
            background-color: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-color);
        }
        
        .toolbar button:hover {
            background-color: var(--border-color);
        }
        
        .status-bar {
            padding: 5px 10px;
            font-size: 0.85rem;
            background-color: var(--bg-color);
            border: 1px solid var(--border-color);
            border-top: none;
            border-radius: 0 0 4px 4px;
            display: flex;
            justify-content: space-between;
        }
        
        .footer {
            margin-top: auto;
            padding: 20px 0;
            background-color: var(--bg-color);
            border-top: 1px solid var(--border-color);
            font-size: 0.9rem;
        }
        
        .alert {
            animation: fadeOut 5s forwards;
        }
        
        @keyframes fadeOut {
            0% { opacity: 1; }
            70% { opacity: 1; }
            100% { opacity: 0; }
        }
        
        @media (max-width: 768px) {
            .toolbar button span {
                display: none;
            }
            
            #note-title {
                margin-bottom: 10px;
            }
            
            .toolbar-action-group {
                margin-top: 10px;
            }
        }
    </style>
</head>
<body class="<?php echo $theme === 'dark' ? 'dark-theme' : ''; ?>">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-journal-text me-2"></i>Online Notepad
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <form method="post" class="d-inline">
                            <input type="hidden" name="current_theme" value="<?php echo $theme; ?>">
                            <button type="submit" name="toggle_theme" class="btn btn-outline-secondary btn-sm">
                                <?php if ($theme === 'light'): ?>
                                    <i class="bi bi-moon-fill"></i> Dark Mode
                                <?php else: ?>
                                    <i class="bi bi-sun-fill"></i> Light Mode
                                <?php endif; ?>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container my-4" id="editor-container">
        <?php if (!empty($status_message)): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <?php echo $status_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row mb-3">
            <div class="col-md-8">
                <input type="text" id="note-title" name="note-title" class="form-control" 
                       placeholder="Note Title" value="<?php echo htmlspecialchars($note_title); ?>">
            </div>
            <div class="col-md-4 toolbar-action-group">
                <form method="post" class="d-flex gap-2">
                    <input type="hidden" name="note_title" id="save-title-input" value="">
                    <input type="hidden" name="note_content" id="save-content-input" value="">
                    <button type="submit" name="save_note" class="btn btn-primary" id="save-button">
                        <i class="bi bi-save"></i> Save
                    </button>
                    <button type="submit" name="load_note" class="btn btn-secondary">
                        <i class="bi bi-folder2-open"></i> Load
                    </button>
                </form>
            </div>
        </div>

        <div class="toolbar">
            <button onclick="formatText('bold')" class="btn btn-sm"><i class="bi bi-type-bold"></i><span> Bold</span></button>
            <button onclick="formatText('italic')" class="btn btn-sm"><i class="bi bi-type-italic"></i><span> Italic</span></button>
            <button onclick="formatText('underline')" class="btn btn-sm"><i class="bi bi-type-underline"></i><span> Underline</span></button>
            <button onclick="formatText('insertOrderedList')" class="btn btn-sm"><i class="bi bi-list-ol"></i><span> Numbered List</span></button>
            <button onclick="formatText('insertUnorderedList')" class="btn btn-sm"><i class="bi bi-list-ul"></i><span> Bullet List</span></button>
            <button onclick="formatText('justifyLeft')" class="btn btn-sm"><i class="bi bi-text-left"></i><span> Left</span></button>
            <button onclick="formatText('justifyCenter')" class="btn btn-sm"><i class="bi bi-text-center"></i><span> Center</span></button>
            <button onclick="formatText('justifyRight')" class="btn btn-sm"><i class="bi bi-text-right"></i><span> Right</span></button>
        </div>
        
        <div contenteditable="true" id="note-content" class="form-control"><?php echo htmlspecialchars($note_content); ?></div>
        
        <div class="status-bar">
            <span id="character-count">Characters: 0</span>
            <span id="word-count">Words: 0</span>
        </div>

        <div class="d-flex justify-content-between mt-3">
            <form method="post" class="d-inline me-2">
                <input type="hidden" name="note_title" id="clear-title-input" value="">
                <input type="hidden" name="note_content" value="">
                <button type="submit" name="clear" class="btn btn-outline-danger">
                    <i class="bi bi-trash"></i> Clear
                </button>
            </form>
            
            <form method="post" class="d-inline">
                <input type="hidden" name="note_title" id="download-title-input" value="">
                <input type="hidden" name="note_content" id="download-content-input" value="">
                <button type="submit" name="download" class="btn btn-outline-success">
                    <i class="bi bi-download"></i> Download
                </button>
            </form>
        </div>
    </div>

    <!-- Footer with SEO content -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>About Online Notepad</h5>
                    <p>A free, easy-to-use online notepad application that lets you write, edit, format, save, and download text without installing any software.</p>
                </div>
                <div class="col-md-3">
                    <h5>Features</h5>
                    <ul class="list-unstyled">
                        <li>Text formatting</li>
                        <li>Auto-save</li>
                        <li>Dark/Light mode</li>
                        <li>Download notes</li>
                        <li>Responsive design</li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Resources</h5>
                    <ul class="list-unstyled">
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="#">Contact Us</a></li>
                    </ul>
                </div>
            </div>
            <hr>
            <p class="text-center mb-0">&copy; <?php echo date('Y'); ?> Online Notepad. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Initialize elements
        const noteContent = document.getElementById('note-content');
        const noteTitle = document.getElementById('note-title');
        const characterCount = document.getElementById('character-count');
        const wordCount = document.getElementById('word-count');
        const saveButton = document.getElementById('save-button');
        const saveTitleInput = document.getElementById('save-title-input');
        const saveContentInput = document.getElementById('save-content-input');
        const downloadTitleInput = document.getElementById('download-title-input');
        const downloadContentInput = document.getElementById('download-content-input');
        const clearTitleInput = document.getElementById('clear-title-input');
        
        // Update word and character count
        function updateCounts() {
            const text = noteContent.textContent || '';
            characterCount.textContent = `Characters: ${text.length}`;
            
            // Count words (split by whitespace and filter out empty strings)
            const words = text.split(/\s+/).filter(word => word.length > 0);
            wordCount.textContent = `Words: ${words.length}`;
        }
        
        // Format text
        function formatText(command) {
            document.execCommand(command, false, null);
            noteContent.focus();
        }
        
        // Auto-save to localStorage every 30 seconds
        function autoSave() {
            const content = noteContent.innerHTML;
            const title = noteTitle.value;
            
            if (title) {
                localStorage.setItem('notepad_title', title);
            }
            
            if (content && content !== '<br>') {
                localStorage.setItem('notepad_content', content);
                console.log('Auto-saved');
            }
        }
        
        // Load from localStorage on page load
        function loadFromStorage() {
            const savedTitle = localStorage.getItem('notepad_title');
            const savedContent = localStorage.getItem('notepad_content');
            
            if (savedTitle && !noteTitle.value) {
                noteTitle.value = savedTitle;
            }
            
            if (savedContent && noteContent.innerHTML === '') {
                noteContent.innerHTML = savedContent;
                updateCounts();
            }
        }
        
        // Before form submission, update hidden inputs
        function updateFormInputs() {
            saveTitleInput.value = noteTitle.value;
            saveContentInput.value = noteContent.innerHTML;
            downloadTitleInput.value = noteTitle.value;
            downloadContentInput.value = noteContent.textContent;
            clearTitleInput.value = noteTitle.value;
        }
        
        // Event listeners
        noteContent.addEventListener('input', updateCounts);
        noteContent.addEventListener('keyup', autoSave);
        noteTitle.addEventListener('change', autoSave);
        
        // Form submission handling
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                updateFormInputs();
            });
        });
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadFromStorage();
            updateCounts();
            
            // Set autoSave interval
            setInterval(autoSave, 30000);
            
            // Set focus to the content area or title if empty
            if (!noteTitle.value) {
                noteTitle.focus();
            } else {
                noteContent.focus();
            }
        });
    </script>
</body>
</html>