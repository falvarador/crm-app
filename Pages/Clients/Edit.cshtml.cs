using CrmApp.Data;
using CrmApp.Data.Models;
using Microsoft.AspNetCore.Mvc;
using Microsoft.AspNetCore.Mvc.RazorPages;
using Microsoft.AspNetCore.Mvc.Rendering;
using Microsoft.EntityFrameworkCore;

namespace CrmApp.Pages.Clients;

public class EditModel(ApplicationDbContext context) : PageModel
{
    [BindProperty]
    public Client Client { get; set; } = null!;

    public async Task<IActionResult> OnGetAsync(int? id)
    {
        if (id == null)
        {
            return NotFound();
        }

        var client =  await context.Client.FirstOrDefaultAsync(m => m.Id == id);
        if (client == null)
        {
            return NotFound();
        }
        Client = client;
        ViewData["UserId"] = new SelectList(context.Users, "Id", "Id");
        return Page();
    }

    // To protect from overposting attacks, enable the specific properties you want to bind to.
    // For more information, see https://aka.ms/RazorPagesCRUD.
    public async Task<IActionResult> OnPostAsync()
    {
        if (!ModelState.IsValid)
        {
            return Page();
        }

        context.Attach(Client).State = EntityState.Modified;

        try
        {
            await context.SaveChangesAsync();
        }
        catch (DbUpdateConcurrencyException)
        {
            if (!ClientExists(Client.Id))
            {
                return NotFound();
            }

            throw;
        }

        return RedirectToPage("./Index");
    }

    private bool ClientExists(int id)
    {
        return context.Client.Any(e => e.Id == id);
    }
}