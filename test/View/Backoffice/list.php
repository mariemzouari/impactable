<?php ?>

<h1>Liste des événements</h1>

<table border="1" width="100%">
    <tr>
        <th>ID</th>
        <th>Titre</th>
        <th>Date</th>
        <th>Catégorie</th>
        <th>Description</th>
        <th>Actions</th>
    </tr>

    <?php if (!empty($events)) : ?>
        <?php foreach ($events as $event) : ?>
            <tr>
                <td><?= $event['id'] ?></td>
                <td><?= $event['titre'] ?></td>
                <td><?= $event['date_event'] ?></td>
                <td><?= $event['categorie'] ?></td>
                <td><?= $event['description'] ?></td>

                <td>
                    <a href="index.php?action=edit&id=<?= $event['id'] ?>">Modifier</a>
                    |
                    <a href="index.php?action=delete&id=<?= $event['id'] ?>"
                       onclick="return confirm('Supprimer ?')">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else : ?>
        <tr>
            <td colspan="6" style="text-align:center;">Aucun événement trouvé.</td>
        </tr>
    <?php endif; ?>

</table>
