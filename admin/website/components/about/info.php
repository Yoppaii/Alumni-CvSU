<?php
require_once 'main_db.php';

function getInstitutionalInfo()
{
    global $mysqli; // Use the existing mysqli connection from main_db.php

    $query = "SELECT * FROM institutional_info ORDER BY display_order";
    $stmt = $mysqli->prepare($query);
    if (!$stmt) {
        die("Prepare failed: " . $mysqli->error);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[$row['category']] = $row;
    }

    $stmt->close();
    return $data;
}

function getCoreValues()
{
    global $mysqli; // Use the existing mysqli connection from main_db.php

    $query = "SELECT * FROM core_values ORDER BY display_order";
    $stmt = $mysqli->prepare($query);
    if (!$stmt) {
        die("Prepare failed: " . $mysqli->error);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    $stmt->close();
    return $data;
}

$institutionalInfo = getInstitutionalInfo();
$coreValues = getCoreValues();
?>

<div class="info-area">
    <h2 class="info-heading">Mandate, Mission, and Vision</h2>

    <div class="info-top-grid">
        <!-- Mandate Card -->
        <?php if (isset($institutionalInfo['mandate'])): ?>
            <div class="info-portrait-card">
                <h3 class="info-portrait-title"><?= htmlspecialchars($institutionalInfo['mandate']['title']) ?></h3>
                <p class="info-portrait-text">
                    <?= htmlspecialchars($institutionalInfo['mandate']['content']) ?>
                </p>
                <?php if (!empty($institutionalInfo['mandate']['translation_content'])): ?>
                    <blockquote class="info-blockquote">
                        <?php if (!empty($institutionalInfo['mandate']['translation_title'])): ?>
                            <strong class="info-lang-label"><?= htmlspecialchars($institutionalInfo['mandate']['translation_title']) ?>:</strong>
                        <?php endif; ?>
                        "<?= htmlspecialchars($institutionalInfo['mandate']['translation_content']) ?>"
                    </blockquote>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Mission Card -->
        <?php if (isset($institutionalInfo['mission'])): ?>
            <div class="info-portrait-card">
                <h3 class="info-portrait-title"><?= htmlspecialchars($institutionalInfo['mission']['title']) ?></h3>
                <p class="info-portrait-text">
                    <?= htmlspecialchars($institutionalInfo['mission']['content']) ?>
                </p>
                <?php if (!empty($institutionalInfo['mission']['translation_content'])): ?>
                    <blockquote class="info-blockquote">
                        <?php if (!empty($institutionalInfo['mission']['translation_title'])): ?>
                            <strong class="info-lang-label"><?= htmlspecialchars($institutionalInfo['mission']['translation_title']) ?>:</strong>
                        <?php endif; ?>
                        "<?= htmlspecialchars($institutionalInfo['mission']['translation_content']) ?>"
                    </blockquote>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Vision Card -->
        <?php if (isset($institutionalInfo['vision'])): ?>
            <div class="info-portrait-card">
                <h3 class="info-portrait-title"><?= htmlspecialchars($institutionalInfo['vision']['title']) ?></h3>
                <p class="info-portrait-text">
                    <?= htmlspecialchars($institutionalInfo['vision']['content']) ?>
                </p>
                <?php if (!empty($institutionalInfo['vision']['translation_content'])): ?>
                    <blockquote class="info-blockquote">
                        <?php if (!empty($institutionalInfo['vision']['translation_title'])): ?>
                            <strong class="info-lang-label"><?= htmlspecialchars($institutionalInfo['vision']['translation_title']) ?>:</strong>
                        <?php endif; ?>
                        "<?= htmlspecialchars($institutionalInfo['vision']['translation_content']) ?>"
                    </blockquote>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Core Values Section -->
    <?php if (!empty($coreValues)): ?>
        <section class="info-section">
            <h3 class="info-section-title">Core Values</h3>
            <div class="info-values-grid">
                <?php foreach ($coreValues as $value): ?>
                    <div class="info-value-card">
                        <i class="<?= htmlspecialchars($value['icon_class']) ?> info-value-icon"></i>
                        <h4 class="info-value-title"><?= htmlspecialchars($value['title']) ?></h4>
                        <p class="info-value-text"><?= htmlspecialchars($value['description']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- Quality Policy Section -->
    <?php if (isset($institutionalInfo['quality_policy'])): ?>
        <section class="info-section info-quality-section">
            <h3 class="info-section-title"><?= htmlspecialchars($institutionalInfo['quality_policy']['title']) ?></h3>
            <p class="info-text">
                <?= htmlspecialchars($institutionalInfo['quality_policy']['content']) ?>
            </p>
        </section>
    <?php endif; ?>
</div>