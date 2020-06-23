<form action="/send-recipe" method="POST">
    <fieldset>
        <legend>
            <h2>Criar Receita</h2>
        </legend>
        <legend>
            <label for="name">Nome da Receita</label>
            <input type="text" name="name" id="recipe_name" placeholder="Nome da Receita" required>
        </legend>
        <legend>
            <label for="ingredients">Ingredientes</label>
            <input type="text" name="ingredients" id="recipe_ingredients" placeholder="Ingredientes" required>
        </legend>
        <legend>
            <label for="recipe">Modo de Preparo</label>
            <textarea name="recipe" id="" cols="30" rows="10" required></textarea>
        </legend>
    </fieldset>
    <button type="submit">Enviar Receita</button>
</form>