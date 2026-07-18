using Microsoft.AspNetCore.Identity;

namespace CrmApp.Data.Models;

public enum FollowUpStatus
{
    Pending,
    Completed,
    Cancelled
}

public class FollowUp
{
    public int Id { get; set; }
    public string Subject { get; set; } = string.Empty;
    public string Description { get; set; } = string.Empty;
    public DateTime FollowUpDate { get; set; }
    public FollowUpStatus Status { get; set; } = FollowUpStatus.Pending;

    public string? ClientId { get; set; }
    
    public required Client Client { get; set; }

    public string? UserId { get; set; }

    public required IdentityUser User { get; set; }
    
    public DateTime CreatedOn { get; set; }
    public DateTime UpdatedOn { get; set; }
}